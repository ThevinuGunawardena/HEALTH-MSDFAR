<?php

namespace common\components;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\ExportCompany;
use backend\models\ProfileOfficers;
use common\models\User;
use Yii;

/**
 * HealthSsoHelper
 * 
 * Generates HMAC-SHA256 Signed JWT SSO tokens to seamlessly authenticate 
 * MSDFAR users into the HEALTH certificate portal (subdomain: health.msdfar.com).
 */
class HealthSsoHelper
{
    /**
     * URL-safe Base64 encoder without padding
     */
    public static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Generate signed JWT (HS256) token using native PHP
     *
     * @param array $payload Key-value claims
     * @param string|null $secretKey Shared secret key (defaults to params['healthSsoSecret'])
     * @return string Signed JWT string (header.payload.signature)
     */
    public static function generateToken(array $payload, ?string $secretKey = null): string
    {
        if ($secretKey === null) {
            $secretKey = Yii::$app->params['healthSsoSecret'] ?? 'GiveASecretKeyHAVINGAtLeast32Characters';
        }

        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        $encodedHeader = self::base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES));
        $encodedPayload = self::base64UrlEncode(json_encode($payload, JSON_UNESCAPED_SLASHES));

        $signature = hash_hmac('sha256', "{$encodedHeader}.{$encodedPayload}", $secretKey, true);
        $encodedSignature = self::base64UrlEncode($signature);

        return "{$encodedHeader}.{$encodedPayload}.{$encodedSignature}";
    }

    /**
     * Build standard SSO payload from a logged in MSDFAR User identity
     *
     * @param User|mixed $user
     * @return array
     */
    public static function buildUserPayload($user): array
    {
        $now = time();
        $userTypes = explode(',', (string)$user->type);

        // Determine mapped role in HEALTH (.NET Identity: 'Company', 'Admin', 'User')
        $isCompany = in_array((string)Constant::EXPORT_COMPANY, $userTypes, true)
            || in_array((string)Constant::EXPORTER, $userTypes, true);

        $isAdmin = in_array((string)Constant::DG, $userTypes, true)
            || in_array((string)Constant::AD, $userTypes, true)
            || in_array((string)Constant::ITD, $userTypes, true)
            || in_array((string)Constant::ADMINISTRATION, $userTypes, true)
            || in_array((string)Constant::DIRECTOR, $userTypes, true)
            || in_array((string)Constant::QUALITY_EXPORT_OFFICER, $userTypes, true)
            || in_array((string)Constant::MEA, $userTypes, true)
            || in_array((string)Constant::ADMIN, $userTypes, true)
            || strcasecmp((string)$user->nic, 'adminHEALTH') === 0
            || strcasecmp((string)$user->nic, 'adminDFAR') === 0;

        $role = $isCompany ? 'Company' : ($isAdmin ? 'Admin' : 'User');

        // Extract Name & Contact Info
        $fullName = '';
        $companyName = null;
        $companyRegNo = null;

        if (strcasecmp((string)$user->nic, 'adminHEALTH') === 0) {
            $fullName = 'Health Administrator';
        } elseif ($isCompany && !empty($user->profile_id)) {
            $company = ExportCompany::findOne($user->profile_id);
            if ($company) {
                $companyName = $company->company_name;
                $companyRegNo = $company->br;
                $fullName = $company->company_name;
            }
        } elseif (!empty($user->profile_id)) {
            $officer = ProfileOfficers::findOne($user->profile_id);
            if ($officer) {
                $fullName = trim(($officer->first_name ?? '') . ' ' . ($officer->last_name ?? ''));
            }
        }

        if (empty($fullName)) {
            $fullName = !empty($user->nic) ? 'User ' . $user->nic : 'MSDFAR User ' . $user->id;
        }

        // Email resolution: Map HEALTH Admin and administrators to HEALTH Admin account (admin@gmail.com)
        $email = trim((string)$user->email);
        if (strcasecmp((string)$user->nic, 'adminHEALTH') === 0 || $isAdmin) {
            $email = 'admin@gmail.com';
        } elseif (empty($email)) {
            $nicClean = preg_replace('/[^a-zA-Z0-9]/', '', (string)$user->nic);
            $email = !empty($nicClean) ? "{$nicClean}@msdfar.gov.lk" : "user{$user->id}@msdfar.gov.lk";
        }

        return [
            'iss'          => 'msdfar-portal',
            'aud'          => 'health-msdfar-portal',
            'iat'          => $now,
            'nbf'          => $now - 300,        // 5 minutes clock-skew tolerance
            'exp'          => $now + 300,        // 5 minutes validity
            'sub'          => (string)$user->id,
            'userId'       => (string)$user->id,
            'email'        => $email,
            'name'         => $fullName,
            'role'         => $role,
            'companyName'  => $companyName,
            'companyRegNo' => $companyRegNo,
            'msdfarType'   => (string)$user->type
        ];
    }

    /**
     * Get redirect URL targeting the HEALTH Angular portal with embedded token
     *
     * @param User|mixed $user
     * @param string|null $returnUrl
     * @return string
     */
    public static function getHealthSsoUrl($user, ?string $returnUrl = null): string
    {
        $portalUrl = Yii::$app->params['healthPortalUrl'] ?? 'https://health.msdfar.com/#/auth/sso';
        $payload = self::buildUserPayload($user);
        $token = self::generateToken($payload);

        $url = $portalUrl;
        $separator = (strpos($url, '?') !== false) ? '&' : '?';
        $url .= $separator . 'token=' . urlencode($token);

        if (!empty($returnUrl) && $returnUrl !== $portalUrl && stripos($returnUrl, 'auth/sso') === false && stripos($returnUrl, 'sso-to-health') === false) {
            $url .= '&returnUrl=' . urlencode($returnUrl);
        }

        return $url;
    }

    /**
     * Execute instant redirect to HEALTH portal
     */
    public static function redirectToHealth($user, ?string $returnUrl = null): void
    {
        $targetUrl = self::getHealthSsoUrl($user, $returnUrl);
        Yii::$app->response->redirect($targetUrl)->send();
        exit();
    }
}
