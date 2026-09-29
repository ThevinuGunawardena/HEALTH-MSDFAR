<?php
/**
 * Single Sign-On (SSO) Integration Helper for Main PHP Portal -> DFAR Certificate Portal
 * 
 * Instructions:
 * 1. Install firebase/php-jwt (or use your existing JWT library):
 *    composer require firebase/php-jwt
 * 
 * 2. Configure the shared secret key (must match AppSettings.JWTSecret in .NET appsettings.json):
 *    $secretKey = "GiveASecretKeyHAVINGAtLeast32Characters";
 * 
 * 3. Call redirectToDfarCertificatePortal($currentUser) when the user clicks to access DFAR.
 */

use Firebase\JWT\JWT;

class DfarSsoHelper 
{
    // Subdomain where the DFAR Certificate Angular + .NET system is hosted
    private const DFAR_PORTAL_URL = "https://certificates.yourdomain.com/#/auth/sso";

    // Shared Secret Key configured in MEA.Server appsettings.json
    private const SHARED_SECRET_KEY = "GiveASecretKeyHAVINGAtLeast32Characters";

    /**
     * Generate Signed SSO Token and redirect the user seamlessly to DFAR portal
     *
     * @param array $user Array containing user details from your main PHP database:
     *                    - id (int|string): Unique user ID in PHP system
     *                    - email (string): User email address (e.g. 'exporter@oceannature.com')
     *                    - name (string): Full name of the user / contact person
     *                    - role (string): 'Company' or 'Admin'
     *                    - company_name (string|null): Registered company name
     *                    - company_reg_no (string|null): Official business reg number
     * @param string|null $returnUrl Optional destination path (e.g. '/company-log-dashboard')
     */
    public static function redirectToDfarPortal(array $user, ?string $returnUrl = null): void
    {
        $now = time();

        $payload = [
            'iss'          => 'main-php-system',                  // Issuer identifier
            'aud'          => 'dfar-certificate-system',           // Audience identifier
            'iat'          => $now,                                // Issued at timestamp
            'nbf'          => $now - 5,                            // Not before (clock skew tolerance)
            'exp'          => $now + 300,                          // Expires in 5 minutes (prevents replay attacks)
            'sub'          => (string)($user['email'] ?? $user['id']), // Subject
            'userId'       => (string)$user['id'],
            'email'        => (string)$user['email'],
            'name'         => (string)($user['name'] ?? $user['email']),
            'role'         => (string)($user['role'] ?? 'Company'),
            'companyName'  => $user['company_name'] ?? null,
            'companyRegNo' => $user['company_reg_no'] ?? null
        ];

        // Sign payload with HMAC SHA-256
        $jwtToken = JWT::encode($payload, self::SHARED_SECRET_KEY, 'HS256');

        // Build target URL
        $targetUrl = self::DFAR_PORTAL_URL . '?token=' . urlencode($jwtToken);
        if ($returnUrl) {
            $targetUrl .= '&returnUrl=' . urlencode($returnUrl);
        }

        // Perform instant redirect
        header('Location: ' . $targetUrl);
        exit();
    }
}

// ==============================================================================
// EXAMPLE USAGE IN YOUR PHP CONTROLLER / BUTTON HANDLER:
// ==============================================================================
/*
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: /login.php');
    exit();
}

$currentUser = [
    'id'             => $_SESSION['user']['id'],
    'email'          => $_SESSION['user']['email'],
    'name'           => $_SESSION['user']['full_name'],
    'role'           => $_SESSION['user']['is_admin'] ? 'Admin' : 'Company',
    'company_name'   => $_SESSION['user']['company_name'],
    'company_reg_no' => $_SESSION['user']['company_reg_no']
];

DfarSsoHelper::redirectToDfarPortal($currentUser);
*/
