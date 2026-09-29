<?php

return [
    'adminEmail' => 'IT@fisheriesdept.gov.lk',
    'supportEmail' => 'support@fisheriesdept.gov.lk',

    // This must be an email allowed by your SMTP configuration.
    'senderEmail' => 'noreply@fisheriesdept.gov.lk',
    'senderName' => 'MSDFAR SYSTEM',

    // 300 seconds = 5 minutes.
    'user.passwordResetTokenExpire' => 300,

    'user.passwordMinLength' => 8,
    'bsVersion' => '4.x',

     'blueTrackerUsername' => 'test_srilankaUser',
    'blueTrackerPassword' => 'd3m0.user!SRILANKA#',
    'blueTrackerApiUrl' =>
        'https://bluetraker.net/api.srilanka/api/GetMessages',

    // Health Certificate System (subdomain: health.msdfar.com) SSO Settings
    'healthPortalUrl' => 'https://health.msdfar.com/#/auth/sso',
    'healthPortalLocalUrl' => 'https://localhost:57549/#/auth/sso',
    'healthSsoSecret' => 'GiveASecretKeyHAVINGAtLeast32Characters',
];