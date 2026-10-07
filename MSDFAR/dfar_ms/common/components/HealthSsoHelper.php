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
        // 1. Detect if running in local development environment
        $isLocal = false;
        if (isset(Yii::$app->request) && method_exists(Yii::$app->request, 'getHostInfo')) {
            $host = (string)Yii::$app->request->getHostInfo();
            $isLocal = (stripos($host, 'localhost') !== false || stripos($host, '127.0.0.1') !== false);
        } elseif (isset($_SERVER['HTTP_HOST'])) {
            $isLocal = (stripos($_SERVER['HTTP_HOST'], 'localhost') !== false || stripos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);
        }

        // 2. Resolve SSO target endpoint and internal destination route
        $targetSsoEndpoint = null;
        $destinationPath = null;

        if (!empty($returnUrl)) {
            if (stripos($returnUrl, 'auth/sso') !== false) {
                // returnUrl itself is the SSO gateway endpoint (e.g. from vet-login.ts)
                $targetSsoEndpoint = $returnUrl;
            } elseif (stripos($returnUrl, 'localhost:57549') !== false || stripos($returnUrl, '127.0.0.1:57549') !== false) {
                $targetSsoEndpoint = 'https://localhost:57549/auth/sso';
                $destinationPath = $returnUrl;
            } else {
                $destinationPath = $returnUrl;
            }
        }

        if ($targetSsoEndpoint === null) {
            if ($isLocal && !empty(Yii::$app->params['healthPortalLocalUrl'])) {
                $targetSsoEndpoint = Yii::$app->params['healthPortalLocalUrl'];
            } else {
                $targetSsoEndpoint = Yii::$app->params['healthPortalUrl'] ?? 'https://health.msdfar.com/auth/sso';
            }
        }

        // Normalize legacy hash-based route (/#/auth/sso -> /auth/sso) for Angular path router
        $targetSsoEndpoint = str_replace('/#/auth/sso', '/auth/sso', $targetSsoEndpoint);

        $payload = self::buildUserPayload($user);
        $token = self::generateToken($payload);

        $separator = (strpos($targetSsoEndpoint, '?') !== false) ? '&' : '?';
        $url = $targetSsoEndpoint . $separator . 'token=' . urlencode($token);

        if (!empty($destinationPath) && $destinationPath !== $targetSsoEndpoint && stripos($destinationPath, 'auth/sso') === false && stripos($destinationPath, 'sso-to-health') === false) {
            $url .= '&returnUrl=' . urlencode($destinationPath);
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
