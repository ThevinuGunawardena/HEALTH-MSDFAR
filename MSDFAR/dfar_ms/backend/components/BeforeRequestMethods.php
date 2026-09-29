<?php
namespace backend\components;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\ProfileOfficers;
use common\models\User;
use Yii;
use yii\base\Behavior;
use yii\web\Application;

class BeforeRequestMethods extends Behavior
{
    public function events()
    {
        return [
            Application::EVENT_BEFORE_REQUEST => 'beforeRequest',
        ];
    }

    /**
     * Runs before every request. Two jobs:
     *   1. Apply the chosen language from the 'lang' cookie.
     *   2. Enforce the profile-completion gate (new users must fill in
     *      First Name, Last Name, Mobile Phone and District before they
     *      can use the rest of the system).
     */
    public function beforeRequest()
    {
        $this->changelocale();
        try {
            $this->enforceProfileCompletion();
        } catch (\Throwable $e) {
            // The gate must never break the site. If anything goes wrong,
            // log it and let the request continue normally.
            Yii::error('Profile gate error: ' . $e->getMessage(), 'profile-gate');
        }
    }

    public function changelocale()
    {
        if (Yii::$app->getRequest()->getCookies()->has('lang')) {
            Yii::$app->language = Yii::$app->getRequest()->getCookies()->getValue('lang');
        }
    }

    /**
     * Profile-completion gate.
     *
     * If the logged-in user (excluding Admins) has not completed the minimum
     * required profile fields, redirect them to the profile form and block
     * access to every other page until they save a complete profile.
     *
     * Minimum required: first_name, last_name, mobile_phone, and district
     * (district is skipped for DG / DM, who have no district).
     */
    protected function enforceProfileCompletion()
    {
        $app = Yii::$app;

        // Only applies to the web app, not console commands.
        if (!($app instanceof Application)) {
            return;
        }

        // Guests (not logged in) are handled by normal access rules.
        if ($app->user->isGuest) {
            return;
        }

        // Admins manage other people — they are never gated.
        if (UserTypeUtil::hasType(Constant::ADMIN)) {
            return;
        }

        // Never gate AJAX / PJAX requests. They are not full-page
        // navigations, so they should never be redirected to the profile
        // form — doing so would break in-page AJAX (e.g. the district →
        // divisions dropdown loader) by returning HTML instead of data.
        if ($app->getRequest()->getIsAjax() || $app->getRequest()->getIsPjax()) {
            return;
        }

        // Resolve the current route reliably. At EVENT_BEFORE_REQUEST the
        // controller is usually not created yet, so $app->controller is null.
        // Parse the request directly to get the route — this is what the
        // router itself will resolve the request to.
        $route = '';
        try {
            $resolved = $app->getUrlManager()->parseRequest($app->getRequest());
            if (is_array($resolved) && isset($resolved[0])) {
                $route = (string) $resolved[0];
            }
        } catch (\Throwable $e) {
            $route = (string) $app->requestedRoute;
        }
        $route = trim($route, '/');

        // Routes the user must always be able to reach, otherwise they would
        // be stuck in a redirect loop and could never fill in the form
        // (or log out). Add the profile create/update routes and auth routes.
        $allowedRoutes = [
            'officer/create',
            'officer/update',
            'officer/password-update',
            'site/logout',
            'site/login',
            'site/error',
            'site/index',

        ];

        // Match if the route IS an allowed route, or STARTS WITH one
        // (covers officer/update/5 style routes with an id appended).
        foreach ($allowedRoutes as $allowed) {
            if ($route === $allowed || strpos($route, $allowed . '/') === 0) {
                return;
            }
        }

        // Load the current user record to read profile_id.
        $user = User::findOne(['id' => $app->user->id]);
        if (!$user) {
            return;
        }

        // ── No profile yet → send to the create form ────────────────
        if (empty($user->profile_id)) {
            $app->response->redirect(['/officer/create'])->send();
            $app->end();
            return;
        }

        // ── Profile exists → check the required fields are filled ────
        $profile = ProfileOfficers::findOne($user->profile_id);
        if (!$profile) {
            // profile_id points to nothing — treat as "needs creating".
            $app->response->redirect(['/officer/create'])->send();
            $app->end();
            return;
        }

        if (!$this->isProfileComplete($profile)) {
            // Existing but incomplete → send to the update form
            // (officer/update auto-loads the user's own profile).
            $app->response->redirect(['/officer/update'])->send();
            $app->end();
            return;
        }

        // Profile complete → let the request continue normally.
    }

    /**
     * Returns true when the profile has all the minimum required fields.
     * District is only required when it applies to the user (not DG / DM).
     *
     * @param ProfileOfficers $profile
     * @return bool
     */
    protected function isProfileComplete(ProfileOfficers $profile)
    {
        // Always required
        if (trim((string) $profile->first_name) === '') {
            return false;
        }
        if (trim((string) $profile->last_name) === '') {
            return false;
        }
        if (trim((string) $profile->mobile_phone) === '') {
            return false;
        }
        if (trim((string) $profile->personal_email) === '') {
            return false;
        }

        // District required only for users who have a district
        // (DG and DM are exempt — they manage at the national level).
        $districtApplies = !UserTypeUtil::hasType(Constant::DG)
                           && !UserTypeUtil::hasType(Constant::DM);
        if ($districtApplies && empty($profile->district)) {
            return false;
        }

        return true;
    }
}