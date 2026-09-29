<?php

namespace backend\controllers;

use app\models\Request;
use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\Applicationexportbechedemer;
use backend\models\Applicationexportchank;
use backend\models\Applicationexportlobster;
use backend\models\ApprovalLog;
use backend\models\AuthAssignment;
use backend\models\AuthItem;
use backend\models\BoatNumbers;
use backend\models\DepartureRequests;
use backend\models\DeparureRequestCrew;
use backend\models\ExportCompany;
use backend\models\FishermanRegisterdBoat;
use backend\models\FishermanRegisterdBoatLicense;
use backend\models\Fisrstregwithboatis;
use backend\models\HighseasLicense;
use backend\models\NationalLicense;
use backend\models\ProfileFisherman;
use backend\models\ProfileOfficer;
use backend\models\SignupForm;
use backend\models\Skipper;
use backend\models\SkipperRenew;
use backend\models\User;
use backend\services\CommonService;
use common\components\WebUser;
use common\models\LoginForm;
use Exception;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use backend\components\Controller;

use yii\web\Response;
use backend\components\SecurityHelper;
use Throwable;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'language', 'signup', 'expire', 'license-validation', 'importdata', 'request-password-reset', 'reset-password'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'create-user', 'impersonate', 'stop-impersonating', 'expire'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

   public function actionRequestPasswordReset()
{
    $model = new PasswordResetRequestForm();

    if (
        Yii::$app->request->isPost &&
        $model->load(Yii::$app->request->post()) &&
        $model->validate()
    ) {
        $user = \common\models\User::findOne([
            'status' => \common\models\User::STATUS_ACTIVE,
            'email' => trim($model->email),
        ]);

        if ($user === null) {
            Yii::$app->session->setFlash(
                'error',
                'No active user was found for the provided email address.'
            );

            return $this->redirect(['site/request-password-reset']);
        }

        // Generate a new password reset token.
        $user->generatePasswordResetToken();

        // Get expiry time from params.php.
        $expirySeconds = Yii::$app->params[
            'user.passwordResetTokenExpire'
        ] ?? 300;

        $user->reset_token_expires_at = date(
            'Y-m-d H:i:s',
            time() + $expirySeconds
        );

        if (
            !$user->save(false, [
                'password_reset_token',
                'reset_token_expires_at',
            ])
        ) {
            Yii::$app->session->setFlash(
                'error',
                'Unable to generate the password reset token.'
            );

            return $this->redirect(['site/request-password-reset']);
        }

        // This link is only added to the email.
        $resetLink = Yii::$app->urlManager->createAbsoluteUrl([
            'site/reset-password',
            'token' => $user->password_reset_token,
        ]);

        try {
            $emailSent = Yii::$app->mailer
                ->compose(
                    [
                        'html' => 'passwordResetToken-html',
                        'text' => 'passwordResetToken-text',
                    ],
                    [
                        'user' => $user,
                        'resetLink' => $resetLink,
                    ]
                )
                ->setFrom([
                    Yii::$app->params['senderEmail']
                        => Yii::$app->params['senderName'],
                ])
                ->setTo($user->email)
                ->setSubject('Password Reset Request')
                ->send();
        } catch (\Throwable $exception) {
            Yii::error([
                'message' => 'Password reset email sending failed.',
                'userId' => $user->id,
                'email' => $user->email,
                'exception' => $exception->getMessage(),
            ], __METHOD__);

            $emailSent = false;
        }

        if (!$emailSent) {
            // Remove the token when the email could not be sent.
            $user->password_reset_token = null;
            $user->reset_token_expires_at = null;

            $user->save(false, [
                'password_reset_token',
                'reset_token_expires_at',
            ]);

            Yii::$app->session->setFlash(
                'error',
                'Unable to send the password reset email. Please try again.'
            );

            return $this->redirect(['site/request-password-reset']);
        }

        Yii::$app->session->setFlash(
            'success',
            'A password reset link has been sent to your email address. The link is valid for five minutes.'
        );

        // Redirect without the password reset token.
        return $this->redirect(['site/request-password-reset']);
    }

    return $this->render('requestPasswordResetToken', [
        'model' => $model,
    ]);
}

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
   public function actionResetPassword($token)
{
    $user = \common\models\User::findOne([
        'password_reset_token' => $token,
        'status' => \common\models\User::STATUS_ACTIVE,
    ]);

    // Invalid or nonexistent token.
    if ($user === null) {
        Yii::$app->session->setFlash(
            'error',
            'The password reset token is invalid.'
        );

        return $this->redirect(['site/request-password-reset']);
    }

    // Expired token.
    if (
        empty($user->reset_token_expires_at) ||
        strtotime($user->reset_token_expires_at) <= time()
    ) {
        $user->password_reset_token = null;
        $user->reset_token_expires_at = null;

        $user->save(false, [
            'password_reset_token',
            'reset_token_expires_at',
        ]);

        Yii::$app->session->setFlash(
            'error',
            'The password reset token has expired. Please request a new password reset link.'
        );

        return $this->redirect(['site/request-password-reset']);
    }

    try {
        $model = new ResetPasswordForm($token);
    } catch (InvalidArgumentException $exception) {
        throw new BadRequestHttpException(
            $exception->getMessage()
        );
    }

    if (
        Yii::$app->request->isPost &&
        $model->load(Yii::$app->request->post()) &&
        $model->validate() &&
        $model->resetPassword()
    ) {
        // Clear the custom token expiry value.
        $user->reset_token_expires_at = null;

        if (
            !$user->save(false, [
                'reset_token_expires_at',
            ])
        ) {
            Yii::error([
                'message' => 'Failed to clear reset-token expiry.',
                'userId' => $user->id,
                'errors' => $user->getErrors(),
            ], __METHOD__);
        }

        // Log in the user after resetting the password.
        Yii::$app->user->login($user);

        Yii::$app->session->setFlash(
            'success',
            'Your password has been reset successfully.'
        );

        // All user types go to the application home page.
        return $this->goHome();
    }

    return $this->render('resetPassword', [
        'model' => $model,
    ]);
}


    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest) {
            $this->runOncePerDay();
            Yii::$app->session->set('userPermission', Yii::$app->user->identity->user_permission);

            if (UserTypeUtil::hasType(Constant::FISHERMAN) && Yii::$app->user->identity->profile_id == 0) {
                $profile = ProfileFisherman::find()->where(["nic" => Yii::$app->user->identity->nic])->one();


                //                print_r(!empty($profile));exit();
                if (!empty($profile)) {
                    $user = User::find()->where(["nic" => Yii::$app->user->identity->nic])->one();
                    $user->profile_id = $profile->id;
                    $user->save();
                } else {
                    return $this->redirect("fisherman/profile-edit");
                }
            }
            if (
                UserTypeUtil::hasType(Constant::FISHERMAN) &&
                !UserTypeUtil::hasType(Constant::EXPORT_COMPANY) && Yii::$app->user->identity->profile_id != 0
            ) {
                return $this->redirect("fisherman/profile");
            }
            if (
                !UserTypeUtil::hasType(Constant::FISHERMAN) &&
                !UserTypeUtil::hasType(Constant::EXPORT_COMPANY) && Yii::$app->user->identity->profile_id ==
                0
            ) {
                return $this->redirect("officer/create");
            }

            if (
                !UserTypeUtil::hasType(Constant::FISHERMAN) && !UserTypeUtil::hasType(
                    Constant::EXPORT_COMPANY
                )
            ) {
                $profile = ProfileOfficer::findOne(Yii::$app->user->identity->profile_id);

                Yii::$app->session->set('userPermission', Yii::$app->user->identity->user_permission);
                Yii::$app->session->set('userId', Yii::$app->user->identity->getId());
                Yii::$app->session->set('officer_uid', $profile->id);
                echo "<script>localStorage.setItem('profile_id', '$profile->id');</script>";
                Yii::$app->session->set('officer_district', $profile->district);
                Yii::$app->session->set('officer_division', $profile->division);
                Yii::$app->session->set('officer_harbour', $profile->harbour);

            }
            if (UserTypeUtil::hasType(Constant::EXPORT_COMPANY)) {
                $profile = ExportCompany::findOne(Yii::$app->user->identity->profile_id);
                Yii::$app->session->set('userPermission', Yii::$app->user->identity->user_permission);
                Yii::$app->session->set('userApplications', $profile->appication_types);
                Yii::$app->session->set('userId', Yii::$app->user->identity->getId());
                Yii::$app->session->set('officer_uid', $profile->id);
                echo "<script>localStorage.setItem('profile_id', '$profile->id');</script>";
                Yii::$app->session->set('officer_district', $profile->district);
                Yii::$app->session->set('profile_id', Yii::$app->user->identity->profile_id);
            }
        }
        $user = Yii::$app->user->identity;
        if ($user) {
            switch ($user->type) {
                case Constant::DM:
                    return $this->redirect(['analytics/dm-dashboard']);
                case Constant::DG:
                    return $this->redirect(['analytics/dg-dashboard']);
                case Constant::AD:
                    return $this->redirect(['analytics/ad-dashboard']);
                case Constant::FI:
                    return $this->redirect(['analytics/fi-dashboard']);
                // case Constant::FISHERMAN:
                //     return $this->redirect(['prototype/fm-index']);
                // case Constant::EXPORT_COMPANY:
                //     return $this->redirect(['prototype/export-company-index']);
                default:
                    return $this->render('index');
            }
        }
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // Redirect users to their appropriate dashboards based on user type
        //    $user = Yii::$app->user->identity;
        //    if ($user->type == Constant::HARBOUR_OFFICER) {
        //         return $this->redirect(['e-log-temp/create']);
        //     }
//            if ($user) {
//                switch ($user->type) {
//                    case Constant::DM:
//                        return $this->redirect(['analytics/dm-dashboard']);
//                    case Constant::DG:
//                        return $this->redirect(['analytics/dg-dashboard']);
//                    // case Constant::FI:
//                    //     return $this->redirect(['prototype/fi-index']);
//                    // case Constant::AD:
//                    //     return $this->redirect(['prototype/ad-index']);
//                    // case Constant::FISHERMAN:
//                    //     return $this->redirect(['prototype/fm-index']);
//                    // case Constant::EXPORT_COMPANY:
//                    //     return $this->redirect(['prototype/export-company-index']);
//                    default:
//                        return $this->goBack();
//                }
//            }
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLicenseValidation(
    ?string $token = null,
    ?string $type = null
) {
    $status = 'NOT_AVAILABLE';
    $data = [];
    $query = null;

    $token = trim(
        (string) $token
    );

    $type = strtolower(
        trim(
            (string) $type
        )
    );

    /*
     * ==========================================================
     * BASIC TOKEN VALIDATION
     * ==========================================================
     */

    if ($token === '') {
        return $this->render(
            'license-validator',
            [
                'status' => 'INVALID_TOKEN',
                'data' => [],
            ]
        );
    }

    /*
     * ==========================================================
     * NEW SECURE DEPARTURE REQUEST TOKEN
     * ==========================================================
     *
     * URL:
     *
     * /site/license-validation
     *      ?type=departure-request
     *      &token=<SecurityHelper token>
     *
     * IMPORTANT:
     * SecurityHelper tokens must never pass through hex2bin().
     * ==========================================================
     */

    if ($type === 'departure-request') {
        try {
            $departureRequestId =
                SecurityHelper::decryptId(
                    $token,
                    DepartureRequests::class
                );

            $license =
                DepartureRequests::findOne(
                    (int) $departureRequestId
                );

            if ($license === null) {
                return $this->render(
                    'license-validator',
                    [
                        'status' => 'NOT_AVAILABLE',
                        'data' => [],
                    ]
                );
            }

            $data = [
                'license_type' =>
                    'DEPARTURE REQUEST',

                'license_number' =>
                    (string) (
                        $license->boat_no ?? ''
                    ),

                'boat_number' =>
                    (string) (
                        $license->boat_no ?? ''
                    ),

                'boat_name' =>
                    (string) (
                        $license->boat_name ?? ''
                    ),

                'fisherman_name' =>
                    (string) (
                        $license->owner ?? ''
                    ),

                'fisherman_nic' =>
                    '',

                'owner_name' =>
                    (string) (
                        $license->owner ?? ''
                    ),

                'owner_contact' =>
                    (string) (
                        $license->contact_no ?? ''
                    ),

                'skipper_name' =>
                    (string) (
                        $license->skipper ?? ''
                    ),

                'skipper_nic' =>
                    (string) (
                        $license->skipper_nic ?? ''
                    ),

                'skipper_number' =>
                    (string) (
                        $license->skipper_no ?? ''
                    ),

                'departure_port' =>
                    (string) (
                        $license->harbor ?? ''
                    ),

                'request_date' =>
                    !empty(
                        $license->req_date_time
                    )
                        ? date(
                            'Y-m-d H:i',
                            strtotime(
                                $license->req_date_time
                            )
                        )
                        : '',

                'issue_date' =>
                    !empty(
                        $license->action_date
                    )
                        ? date(
                            'Y-m-d H:i',
                            strtotime(
                                $license->action_date
                            )
                        )
                        : '',
            ];

            if ($license->approve === 'A') {
                $status = 'Approved';

                $data['current_status'] =
                    'APPROVED';
            } elseif (
                $license->approve === 'R'
            ) {
                $status = 'Not_allowed';

                $data['current_status'] =
                    'REJECTED';
            } else {
                $status = 'Not_allowed';

                $data['current_status'] =
                    'PENDING';
            }
        } catch (\Throwable $e) {
            Yii::warning(
                [
                    'message' =>
                        'Invalid secure Departure Request QR token.',

                    'tokenLength' =>
                        strlen($token),

                    'exception' =>
                        $e->getMessage(),
                ],
                __METHOD__
            );

            $status = 'INVALID_TOKEN';
            $data = [];
        }

        /*
         * CRITICAL:
         *
         * Secure tokens must stop here.
         */
        return $this->render(
            'license-validator',
            [
                'status' => $status,
                'data' => $data,
            ]
        );
    }

    /*
     * ==========================================================
     * NEW SECURE SKIPPER / SKIPPER RENEW TOKEN
     * ==========================================================
     *
     * New QR URLs:
     *
     * /site/license-validation
     *      ?type=skipper
     *      &token=<SecurityHelper token>
     *
     * /site/license-validation
     *      ?type=skipper-renew
     *      &token=<SecurityHelper token>
     *
     * IMPORTANT:
     * This block MUST remain before LEGACY QR TOKEN SUPPORT.
     * ==========================================================
     */

    if (
        $type === 'skipper'
        || $type === 'skipper-renew'
    ) {
        try {
            $isRenewal =
                $type === 'skipper-renew';

            /*
             * The class used here must exactly match
             * the class used when creating the token.
             */
            $tokenModelClass =
                $isRenewal
                    ? SkipperRenew::class
                    : Skipper::class;

            $skipperId =
                SecurityHelper::decryptId(
                    $token,
                    $tokenModelClass
                );

            if ($isRenewal) {
                $license =
                    SkipperRenew::findOne(
                        (int) $skipperId
                    );
            } else {
                $license =
                    Skipper::findOne(
                        (int) $skipperId
                    );
            }

            if ($license === null) {
                return $this->render(
                    'license-validator',
                    [
                        'status' =>
                            'NOT_AVAILABLE',

                        'data' =>
                            [],
                    ]
                );
            }

            /*
             * Calculate licence expiry.
             */
            if (
                empty(
                    $license->expire_date
                )
            ) {
                $baseDate =
                    $license->approval_stage ===
                    'Completed'
                        ? $license->approved_time
                        : $license->created;

                $expiryModifier =
                    $license->approval_stage ===
                    'Completed'
                        ? '+1 year'
                        : '+6 months';

                $baseTimestamp =
                    strtotime(
                        (string) $baseDate
                    );

                $expDate =
                    $baseTimestamp !== false
                        ? date(
                            'Y-m-d',
                            strtotime(
                                $expiryModifier,
                                $baseTimestamp
                            )
                        )
                        : null;
            } else {
                $expDate =
                    $license->expire_date;
            }

            $datediff =
                !empty($expDate)
                    ? CommonService::
                        getDateDiffWithCurrentDate(
                            $expDate
                        )
                    : 0;

            /*
             * Both Skipper and SkipperRenew use
             * fisherman_id and expose fisherman relation.
             */
            $fisherman =
                $license->fisherman ?? null;

            $data = [
                'expire_date' =>
                    $expDate,

                'license_number' =>
                    $fisherman !== null
                        ? (string) (
                            $fisherman
                                ->fisherman_uid
                            ?? ''
                        )
                        : '',

                'license_type' =>
                    $isRenewal
                        ? 'Skipper License Renewal'
                        : 'Skipper License',

                'fisherman_name' =>
                    $fisherman !== null
                        ? (string) (
                            $fisherman
                                ->preferred_name_for_id
                            ?? ''
                        )
                        : '',

                'fisherman_nic' =>
                    $fisherman !== null
                        ? (string) (
                            $fisherman->nic
                            ?? ''
                        )
                        : '',

                'current_status' =>
                    strtoupper(
                        Constant::$licenseStatus[
                            $license->status
                        ] ?? 'UNKNOWN'
                    ),
            ];

            $status =
                $datediff > 0
                    ? 'ACTIVE'
                    : 'EXPIRED';
        } catch (\Throwable $e) {
            /*
             * Do not expose cryptographic details publicly.
             */
            Yii::warning(
                [
                    'message' =>
                        'Invalid secure Skipper QR token.',

                    'validationType' =>
                        $type,

                    'tokenLength' =>
                        strlen($token),

                    'exception' =>
                        $e->getMessage(),
                ],
                __METHOD__
            );

            $status =
                'INVALID_TOKEN';

            $data =
                [];
        }

        /*
         * CRITICAL:
         *
         * Secure Skipper tokens stop here.
         *
         * Never allow these tokens to continue to
         * the legacy hex2bin() processing below.
         */
        return $this->render(
            'license-validator',
            [
                'status' =>
                    $status,

                'data' =>
                    $data,
            ]
        );
    }

    /*
     * ==========================================================
     * LEGACY QR TOKEN SUPPORT
     * ==========================================================
     *
     * Existing/old QR codes use:
     *
     * bin2hex(
     *     '{"type":"...","id":"..."}'
     * )
     *
     * Keep this so previously generated licences continue
     * to validate.
     * ==========================================================
     */

    try {
        /*
         * hex2bin requires:
         *
         * - hexadecimal characters only
         * - even number of characters
         */
        if (
            strlen($token) % 2 !== 0
            || !ctype_xdigit($token)
        ) {
            throw new \RuntimeException(
                'Invalid legacy token format.'
            );
        }

        $decodedToken =
            hex2bin(
                $token
            );

        if ($decodedToken === false) {
            throw new \RuntimeException(
                'Unable to decode legacy token.'
            );
        }

        $query =
            json_decode(
                $decodedToken
            );

        if (
            !is_object($query)
            || !isset($query->type)
            || !isset($query->id)
        ) {
            throw new \RuntimeException(
                'Invalid legacy token payload.'
            );
        }
    } catch (\Throwable $e) {
        Yii::warning(
            'Invalid legacy licence validation token: '
            . $e->getMessage(),
            __METHOD__
        );

        return $this->render(
            'license-validator',
            [
                'status' =>
                    'INVALID_TOKEN',

                'data' =>
                    [],
            ]
        );
    }

    /*
     * ==========================================================
     * BOAT REGISTRATION
     * ==========================================================
     */

    if (
        isset($query->type)
        && $query->type ===
            'BOAT_REGISTER'
    ) {
        $license =
            FishermanRegisterdBoatLicense::find()
                ->where([
                    'nid' =>
                        $query->id,
                ])
                ->one();

        if ($license !== null) {
            $datediff =
                CommonService::
                    getDateDiffWithCurrentDate(
                        $license->expire_date
                    );

            $data['expire_date'] =
                $license->expire_date;

            $data['license_number'] =
                $license
                    ->boatNumber
                    ->boat_number;

            $data['license_type'] =
                'Boat Registration';

            $data['fisherman_name'] =
                $license
                    ->fisherman
                    ->preferred_name_for_id;

            $data['fisherman_nic'] =
                $license
                    ->fisherman
                    ->nic;

            $data['current_status'] =
                strtoupper(
                    Constant::$licenseStatus[
                        $license->status
                    ] ?? 'UNKNOWN'
                );

            $status =
                $datediff > 0
                    ? 'ACTIVE'
                    : 'EXPIRED';
        }
    }

    /*
     * ==========================================================
     * BOAT NUMBER
     * ==========================================================
     */

    if (
        isset($query->type)
        && $query->type ===
            'BOAT_NUMBER'
    ) {
        $license =
            BoatNumbers::find()
                ->where([
                    'id' =>
                        $query->id,
                ])
                ->one();

        if ($license !== null) {
            $datediff =
                CommonService::
                    getDateDiffWithCurrentDate(
                        $license->expire_date
                    );

            $data['expire_date'] =
                $license->expire_date;

            $data['license_number'] =
                $license->boat_number;

            $data['license_type'] =
                'Boat Number';

            $data['fisherman_name'] =
                $license
                    ->owner0
                    ->preferred_name_for_id;

            $data['fisherman_nic'] =
                $license
                    ->owner0
                    ->nic;

            $data['current_status'] =
                strtoupper(
                    Constant::$licenseStatus[
                        $license->status
                    ] ?? 'UNKNOWN'
                );

            $status =
                $datediff > 0
                    ? 'ACTIVE'
                    : 'EXPIRED';
        }
    }

    /*
     * ==========================================================
     * HIGH SEAS LICENSE
     * ==========================================================
     */

    if (
        isset($query->type)
        && $query->type ===
            'highseas-license'
    ) {
        $license =
            HighseasLicense::find()
                ->where([
                    'id' =>
                        $query->id,
                ])
                ->one();

        if ($license !== null) {
            $datediff =
                CommonService::
                    getDateDiffWithCurrentDate(
                        $license->expire_date
                    );

            $data['expire_date'] =
                $license->expire_date;

            $data['license_number'] =
                $license->license_number;

            $data['license_type'] =
                'Highseas License';

            $data['fisherman_name'] =
                $license
                    ->fisherman
                    ->preferred_name_for_id;

            $data['fisherman_nic'] =
                $license
                    ->fisherman
                    ->nic;

            $data['current_status'] =
                strtoupper(
                    Constant::$licenseStatus[
                        $license->status
                    ] ?? 'UNKNOWN'
                );

            $status =
                $datediff > 0
                    ? 'ACTIVE'
                    : 'EXPIRED';
        }
    }

    /*
     * ==========================================================
     * NATIONAL LICENSE
     * ==========================================================
     */

    if (
        isset($query->type)
        && $query->type ===
            'national-license'
    ) {
        $license =
            NationalLicense::find()
                ->where([
                    'id' =>
                        $query->id,
                ])
                ->one();

        if ($license !== null) {
            $datediff =
                CommonService::
                    getDateDiffWithCurrentDate(
                        $license->expire_date
                    );

            $data['expire_date'] =
                $license->expire_date;

            $data['license_number'] =
                $license->license_number;

            $data['license_type'] =
                'National License';

            $data['fisherman_name'] =
                $license
                    ->fisherman
                    ->preferred_name_for_id;

            $data['fisherman_nic'] =
                $license
                    ->fisherman
                    ->nic;

            $data['current_status'] =
                strtoupper(
                    Constant::$licenseStatus[
                        $license->status
                    ] ?? 'UNKNOWN'
                );

            $status =
                $datediff > 0
                    ? 'ACTIVE'
                    : 'EXPIRED';
        }
    }

    /*
     * ==========================================================
     * FISHERMAN LICENSE
     * ==========================================================
     */

    if (
        isset($query->type)
        && $query->type ===
            'fisherman-license'
    ) {
        $license =
            ProfileFisherman::find()
                ->where([
                    'id' =>
                        $query->id,
                ])
                ->one();

        if ($license !== null) {
            if (
                empty(
                    $license->expire_date
                )
            ) {
                $baseDate =
                    $license->approval_stage ===
                    'Completed'
                        ? $license->approved_time
                        : $license->created;

                $expiryModifier =
                    $license->approval_stage ===
                    'Completed'
                        ? '+1 year'
                        : '+6 months';

                $baseTimestamp =
                    strtotime(
                        (string) $baseDate
                    );

                $expDate =
                    $baseTimestamp !== false
                        ? date(
                            'Y-m-d',
                            strtotime(
                                $expiryModifier,
                                $baseTimestamp
                            )
                        )
                        : null;
            } else {
                $expDate =
                    $license->expire_date;
            }

            $datediff =
                !empty($expDate)
                    ? CommonService::
                        getDateDiffWithCurrentDate(
                            $expDate
                        )
                    : 0;

            $data['expire_date'] =
                $expDate;

            $data['license_number'] =
                $license->fisherman_uid;

            $data['license_type'] =
                'Fisherman License';

            $data['fisherman_name'] =
                $license
                    ->preferred_name_for_id;

            $data['fisherman_nic'] =
                $license->nic;

            $data['current_status'] =
                strtoupper(
                    Constant::$licenseStatus[
                        $license->status
                    ] ?? 'UNKNOWN'
                );

            $status =
                $datediff > 0
                    ? 'ACTIVE'
                    : 'EXPIRED';
        }
    }

    /*
     * ==========================================================
     * LEGACY SKIPPER LICENSE / SKIPPER RENEW LICENSE
     * ==========================================================
     *
     * Old QR codes only.
     *
     * New QR codes are already handled above using
     * SecurityHelper.
     * ==========================================================
     */

    if (
        isset($query->type)
        && in_array(
            $query->type,
            [
                'skipper-license',
                'skipper-renew',
            ],
            true
        )
    ) {
        $isRenewal =
            $query->type ===
                'skipper-renew';

        if ($isRenewal) {
            $license =
                SkipperRenew::find()
                    ->where([
                        'id' =>
                            (int) $query->id,
                    ])
                    ->one();
        } else {
            $license =
                Skipper::find()
                    ->where([
                        'id' =>
                            (int) $query->id,
                    ])
                    ->one();
        }

        if ($license !== null) {
            if (
                empty(
                    $license->expire_date
                )
            ) {
                $baseDate =
                    $license->approval_stage ===
                    'Completed'
                        ? $license->approved_time
                        : $license->created;

                $expiryModifier =
                    $license->approval_stage ===
                    'Completed'
                        ? '+1 year'
                        : '+6 months';

                $baseTimestamp =
                    strtotime(
                        (string) $baseDate
                    );

                $expDate =
                    $baseTimestamp !== false
                        ? date(
                            'Y-m-d',
                            strtotime(
                                $expiryModifier,
                                $baseTimestamp
                            )
                        )
                        : null;
            } else {
                $expDate =
                    $license->expire_date;
            }

            $datediff =
                !empty($expDate)
                    ? CommonService::
                        getDateDiffWithCurrentDate(
                            $expDate
                        )
                    : 0;

            $fisherman =
                $license->fisherman ?? null;

            $data['expire_date'] =
                $expDate;

            $data['license_number'] =
                $fisherman !== null
                    ? (string) (
                        $fisherman
                            ->fisherman_uid
                        ?? ''
                    )
                    : '';

            $data['license_type'] =
                $isRenewal
                    ? 'Skipper License Renewal'
                    : 'Skipper License';

            $data['fisherman_name'] =
                $fisherman !== null
                    ? (string) (
                        $fisherman
                            ->preferred_name_for_id
                        ?? ''
                    )
                    : '';

            $data['fisherman_nic'] =
                $fisherman !== null
                    ? (string) (
                        $fisherman->nic
                        ?? ''
                    )
                    : '';

            $data['current_status'] =
                strtoupper(
                    Constant::$licenseStatus[
                        $license->status
                    ] ?? 'UNKNOWN'
                );

            $status =
                $datediff > 0
                    ? 'ACTIVE'
                    : 'EXPIRED';
        }
    }

    /*
     * ==========================================================
     * EXPORT BECHE-DE-MERS
     * ==========================================================
     */

    if (
        isset($query->type)
        && $query->type ===
            'ExportBedchamber'
    ) {
        $license =
            Applicationexportbechedemer::find()
                ->where([
                    'id' =>
                        $query->id,
                ])
                ->one();

        if ($license !== null) {
            $datediff =
                CommonService::
                    getDateDiffWithCurrentDate(
                        $license->expire_date
                    );

            $data['expire_date'] =
                $license->expire_date;

            $data['license_type'] =
                'Export beche-de-mers';

            $status =
                $datediff > 0
                    ? 'ACTIVE'
                    : 'EXPIRED';
        }
    }

    /*
     * ==========================================================
     * EXPORT CHANK
     * ==========================================================
     */

    if (
        isset($query->type)
        && $query->type ===
            'ExportChank'
    ) {
        $license =
            Applicationexportchank::find()
                ->where([
                    'id' =>
                        $query->id,
                ])
                ->one();

        if ($license !== null) {
            $datediff =
                CommonService::
                    getDateDiffWithCurrentDate(
                        $license->expire_date
                    );

            $data['expire_date'] =
                $license->expire_date;

            $data['license_type'] =
                'Export Chank';

            $status =
                $datediff > 0
                    ? 'ACTIVE'
                    : 'EXPIRED';
        }
    }

    /*
     * ==========================================================
     * EXPORT LOBSTER
     * ==========================================================
     */

    if (
        isset($query->type)
        && $query->type ===
            'ExportLobster'
    ) {
        $license =
            Applicationexportlobster::find()
                ->where([
                    'id' =>
                        $query->id,
                ])
                ->one();

        if ($license !== null) {
            $datediff =
                CommonService::
                    getDateDiffWithCurrentDate(
                        $license->expire_date
                    );

            $data['expire_date'] =
                $license->expire_date;

            $data['license_type'] =
                'Export Lobster';

            $data['license_number'] =
                date('Y')
                . '/'
                . $license->id
                . 'E';

            $status =
                $datediff > 0
                    ? 'ACTIVE'
                    : 'EXPIRED';
        }
    }

    /*
     * ==========================================================
     * LEGACY DEPARTURE REQUEST QR
     * ==========================================================
     *
     * Keeps previously generated Departure PDFs working.
     * ==========================================================
     */

    if (
        isset($query->type)
        && $query->type ===
            'DEPARTURE_REQUEST'
    ) {
        $license =
            DepartureRequests::find()
                ->where([
                    'id' =>
                        $query->id,
                ])
                ->one();

        if ($license !== null) {
            $data['license_type'] =
                'DEPARTURE REQUEST';

            $data['license_number'] =
                (string) (
                    $license->boat_no ?? ''
                );

            $data['boat_number'] =
                (string) (
                    $license->boat_no ?? ''
                );

            $data['boat_name'] =
                (string) (
                    $license->boat_name ?? ''
                );

            $data['fisherman_name'] =
                (string) (
                    $license->owner ?? ''
                );

            $data['owner_name'] =
                (string) (
                    $license->owner ?? ''
                );

            $data['skipper_name'] =
                (string) (
                    $license->skipper ?? ''
                );

            $data['skipper_nic'] =
                (string) (
                    $license->skipper_nic ?? ''
                );

            $data['departure_port'] =
                (string) (
                    $license->harbor ?? ''
                );

            if ($license->approve === 'A') {
                $status =
                    'Approved';

                $data['current_status'] =
                    'APPROVED';
            } elseif (
                $license->approve === 'R'
            ) {
                $status =
                    'Not_allowed';

                $data['current_status'] =
                    'REJECTED';
            } else {
                $status =
                    'Not_allowed';

                $data['current_status'] =
                    'PENDING';
            }
        }
    }

    /*
     * ==========================================================
     * FINAL RESPONSE
     * ==========================================================
     */

    return $this->render(
        'license-validator',
        [
            'status' => $status,
            'data' => $data,
        ]
    );
}

    public function actionImportdata2($page, $size = 10)
    {

//        $boats = BoatReg::find()->limit(500)
//            ->offset($page * 500)
//            ->all();
//
//        foreach ($boats as $boat) {
//            $boatNo = BoatNumbers::find()->where(['boat_number' => $boat->boat_no, 'status' => Constant::Active])->one();
//            if (!empty($boatNo)) {
//                $boatReg = DepartureBoats::find()->where(['boat_number_id' => $boatNo->id])->one();
//                if (!empty($boatReg)) {
//                    $boatReg->status = $boat->status;
//                    $boatReg->timestamp = $boat->timestamp;
//                    $boatReg->harbor = $boat->harbor;
//                    $boatReg->district = $boat->district;
//                    $boatReg->date_violation = $boat->date_violation;
//                    $boatReg->dep_cancelled_by = $boat->dep_cancelled_by;
//                    $boatReg->remarks = $boat->remarks;
//                    $boatReg->offence = $boat->offence;
//                    if (!empty($boat->to_date))
//                        $boatReg->to_date = $boat->to_date;
//                    $boatReg->save(false);
//                    print_r("\nboatReg updated " . $boat->boat_no);
//                } else {
//                    print_r("\nboatReg not found " . $boat->boat_no);
//                }
//
//            } else {
//                print_r("\nboatReg number found " . $boat->boat_no);
//            }
        $requests = Request::find()->limit($size)
            ->offset($page * $size)
            ->all();
        foreach ($requests as $request) {
            $depRequest = new DepartureRequests();
            $depRequest->id = $request->id;
            $depRequest->boat_no = $request->boat_no;
            $depRequest->boat_name = $request->boat_name;
            $depRequest->owner = $request->owner;
            $depRequest->contact_no = $request->contact_no;
            $depRequest->email = $request->email;
            $depRequest->skipper_no = $request->skipper_no;
            $depRequest->skipper = $request->skipper;
            $depRequest->skipper_nic = $request->skipper_nic;
            $depRequest->district = $request->district;
            $depRequest->harbor = $request->harbor;
            $depRequest->fishing_area = $request->fishing_area;
            $depRequest->length_longline = $request->length_longline;
            $depRequest->length_gillnet = $request->length_gillnet;
            $depRequest->length_ringnet = $request->length_ringnet;
            $depRequest->longline_hooks = $request->longline_hooks;
            $depRequest->mesh_gillnet = $request->mesh_gillnet;
            $depRequest->mesh_ringnet = $request->mesh_ringnet;
            $depRequest->national_license_no = $request->national_license_no;
            $depRequest->hs_license_no = $request->hs_license_no;
            $depRequest->vms = $request->vms;
            $depRequest->agree = $request->agree;
            $depRequest->req_date_time = $request->req_date_time;
            $depRequest->user = $request->user;
            $depRequest->action_date = $request->action_date;
            $depRequest->approve = $request->approve;
            $depRequest->remarks = $request->remarks;
            $depRequest->water_bot = $request->water_bot;
            $depRequest->mcs = $request->mcs;
            $depRequest->frequency = $request->frequency;
            $depRequest->vms_code = $request->vms_code;
            $depRequest->manual = $request->manual;
            $depRequest->arrivalPort = $request->arrivalPort;
            $depRequest->arrivalDate = $request->arrivalDate;
            $depRequest->arrTime = $request->arrTime;
            $depRequest->save(false);

            if (!empty($request->crew1)) {
                $crew = new DeparureRequestCrew();
                $crew->request_id = $depRequest->id;
                $crew->nic = $request->crew1;
                $crew->name = $request->crew1_id;
                $crew->save(false);
            }
            if (!empty($request->crew2)) {
                $crew = new DeparureRequestCrew();
                $crew->request_id = $depRequest->id;
                $crew->nic = $request->crew2;
                $crew->name = $request->crew2_id;
                $crew->save(false);
            }
            if (!empty($request->crew3)) {
                $crew = new DeparureRequestCrew();
                $crew->request_id = $depRequest->id;
                $crew->nic = $request->crew3;
                $crew->name = $request->crew3_id;
                $crew->save(false);
            }
            if (!empty($request->crew4)) {
                $crew = new DeparureRequestCrew();
                $crew->request_id = $depRequest->id;
                $crew->nic = $request->crew4;
                $crew->name = $request->crew4_id;
                $crew->save(false);
            }
            if (!empty($request->crew5)) {
                $crew = new DeparureRequestCrew();
                $crew->request_id = $depRequest->id;
                $crew->nic = $request->crew5;
                $crew->name = $request->crew5_id;
                $crew->save(false);
            }
            if (!empty($request->crew6)) {
                $crew = new DeparureRequestCrew();
                $crew->request_id = $depRequest->id;
                $crew->nic = $request->crew6;
                $crew->name = $request->crew6_id;
                $crew->save(false);
            }
            if (!empty($request->crew7)) {
                $crew = new DeparureRequestCrew();
                $crew->request_id = $depRequest->id;
                $crew->nic = $request->crew7;
                $crew->name = $request->crew7_id;
                $crew->save(false);
            }
            if (!empty($request->crew8)) {
                $crew = new DeparureRequestCrew();
                $crew->request_id = $depRequest->id;
                $crew->nic = $request->crew8;
                $crew->name = $request->crew8_id;
                $crew->save(false);
            }

            print_r("\nRequest inserted " . $request->id);


        }


//        }
        exit();
//        print_r($boatReg)
    }

    public function actionExpire()
    {
        BoatRegistrationController::markeAsExpired();
        HighseasLicenseController::markeAsExpired();
        NationalLicenseController::markeAsExpired();
        FishermanController::markeAsExpired();

        exit();
    }

    public function actionImportdata22()
    {
        $profile_fisherman = array(
            array('id' => '2021', 'district' => '4'),
            array('id' => '2022', 'district' => '4'),
            array('id' => '2023', 'district' => '4'),
            array('id' => '2024', 'district' => '4'),
            array('id' => '2025', 'district' => '4'),
            array('id' => '2026', 'district' => '4'),
            array('id' => '2027', 'district' => '4'),
            array('id' => '2028', 'district' => '4'),
            array('id' => '2029', 'district' => '4'),
            array('id' => '2030', 'district' => '4'),
            array('id' => '2031', 'district' => '4'),
            array('id' => '2032', 'district' => '4'),
            array('id' => '2033', 'district' => '4'),
        );
        foreach ($profile_fisherman as $key) {
            if ($key['district'] == 10) {
                CommonService::addApprovalLogdone("FISHERMAN-REG", "approve", "f approved", $key['id'], 34);
            }
            if ($key['district'] == 14) {
                CommonService::addApprovalLogdone("FISHERMAN-REG", "approve", "f approved", $key['id'], 149);
            }
            if ($key['district'] == 17) {
                CommonService::addApprovalLogdone("FISHERMAN-REG", "approve", "f approved", $key['id'], 293);
            }
            if ($key['district'] == 6) {
                CommonService::addApprovalLogdone("FISHERMAN-REG", "approve", "f approved", $key['id'], 305);
            }
            if ($key['district'] == 19) {
                CommonService::addApprovalLogdone("FISHERMAN-REG", "approve", "f approved", $key['id'], 338);
            }
            if ($key['district'] == 8) {
                CommonService::addApprovalLogdone("FISHERMAN-REG", "approve", "f approved", $key['id'], 339);
            }
            if ($key['district'] == 7) {
                CommonService::addApprovalLogdone("FISHERMAN-REG", "approve", "f approved", $key['id'], 361);
            }
            if ($key['district'] == 16) {
                CommonService::addApprovalLogdone("FISHERMAN-REG", "approve", "f approved", $key['id'], 495);
            }
            if ($key['district'] == 5) {
                CommonService::addApprovalLogdone("FISHERMAN-REG", "approve", "f approved", $key['id'], 501);
            }
            if ($key['district'] == 4) {
                CommonService::addApprovalLogdone("FISHERMAN-REG", "approve", "f approved", $key['id'], 518);
            }
            if ($key['district'] == 15) {
                CommonService::addApprovalLogdone("FISHERMAN-REG", "approve", "f approved", $key['id'], 625);
            }
            if ($key['district'] == 11) {
                CommonService::addApprovalLogdone("FISHERMAN-REG", "approve", "f approved", $key['id'], 662);
            }
            if ($key['district'] == 12) {
                CommonService::addApprovalLogdone("FISHERMAN-REG", "approve", "f approved", $key['id'], 3067);
            }
            if ($key['district'] == 9) {
                CommonService::addApprovalLogdone("FISHERMAN-REG", "approve", "f approved", $key['id'], 3252);
            }
            if ($key['district'] == 13) {
                CommonService::addApprovalLogdone("FISHERMAN-REG", "approve", "f approved", $key['id'], 5268);
            }

        }
        exit();
        //        $logs = Files::find()->where(["type" => 3])->all();
//
////        print_r($logs);
//        foreach ($logs as $log) {
//            $isthere = FishermanRegisterdBoatLicense::find()->where(['id' => $log->process_id])->one();
//            print_r($isthere);
//            print_r("-----------------------------------------------------");
//
//        $models = ApprovalLog::find()->select('process_id')->where(["type" => "FISHERMAN-REG", "status" => "approve"])->all();
//
//        $id = [];
//        foreach ($models as $fisherman) {
//            $id[] = $fisherman->process_id;
//        }
    }

    /**
     * Logout action.
     *
     * @return Response
     */
  public function actionLogout()
{
    Yii::$app->user->logout(true);

    return $this->goHome();
}


    public function actionLanguage($id = "en")
    {

        $cookie = new yii\web\Cookie([
            'name' => 'lang',
            'value' => $id
        ]);
        Yii::$app->language = $id;
        Yii::$app->getResponse()->getCookies()->add($cookie);
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
    }

    public function actionCreateUser()
    {
        CommonService::validatePermission($this, "admin");

        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $user = $model->signupOfficers()) {
            $authItem = AuthItem::findOne(Constant::$userTypes[$user->type]['name']);
            $auth = new AuthAssignment();
            $auth->user_id = $user->id;
            $auth->item_name = $authItem->name;
            $auth->save();
            //            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->redirect("create-user");
        }

        return $this->render('create-user', [
            'model' => $model,
        ]);
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signupFisherman()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }


    public function actionImpersonate($id)
    {
        /** @var WebUser $webUser */
        $webUser = Yii::$app->getUser();

        $mainIdentityId = $webUser->getMainIdentityId();

        if ($mainIdentityId != $id) {
            /** @var User $user */
            $user = User::findOne($id);

            if ($user->nic != null) {
                $webUser->login($this->getUser($user->nic), $duration = 0);
                $webUser->setMainIdentityId($mainIdentityId);
                return $this->redirect(['/land/user/to/route']);
            }


        }


    }

    /**
     * Finds user by [[username]]
     *
     * @return \common\models\User|null
     */
    protected function getUser($nic)
    {

        $user = \common\models\User::findByUsername($nic);


        return $user;
    }

    public function actionStopImpersonating()
    {
        /** @var WebUser $webUser */
        $webUser = Yii::$app->getUser();
        $mainIdentityId = $webUser->getMainIdentityId();

        if (!empty($mainIdentityId)) {
            CommonService::addImpersonateLog(Yii::$app->user->identity->profile_id, $mainIdentityId, "Ended");

            /** @var User $user */
            $user = User::findOne($mainIdentityId);

            $webUser->login($this->getUser($user->nic), $duration = 0);
            $webUser->setMainIdentityId(null);


        }

        return $this->redirect(['/site/index']);
    }

    public function actionImport()
    {
        $dateof = Fisrstregwithboatis::find()->where(["is not", "last_modified_time", null])->all();
        foreach ($dateof as $item) {
            $ids[] = $item->id;
        }
        print_r(sizeof($dateof));

        $date = FishermanRegisterdBoat::find()->where(["date_of_first_registration" => null])->andWhere(["IN", "id", $ids])->limit(100)->all();
        print_r(sizeof($date));
        foreach ($date as $item) {
            $dateof = Fisrstregwithboatis::find()->where(["id" => $item->id])->one();
            $item->date_of_first_registration = date("Y-m-d", strtotime($dateof->last_modified_time));
            $item->save(false);
            print_r($item);
            print_r("\n");
        }


    }

    public function actionImportdata($startfrom, $autonumber)
    {
        $licenses = FishermanRegisterdBoatLicense::find()->where(['>=', 'nid', $startfrom])->limit(100)->all();
        foreach ($licenses as $licens) {
            $rowId = $licens->nid;

            $logs = ApprovalLog::find()->where(['process_id' => $rowId])->all();
            $licens->nid = $autonumber;
//            if (!empty($licens->license_number)) {
//                $newlicense_number = str_replace($rowId . "", $autonumber . "", $license_number);
//                $licens->license_number = $newlicense_number;
//            }

            $licens->save(false);
            foreach ($logs as $log) {
                if (!empty($log)) {
                    $log->process_id = $autonumber;
                    $log->save(false);
                }
            }

            $autonumber++;

        }
    }


    function runOncePerDay()
    {
        $key = 'daily_task';
        $today = date('Y-m-d');

        $row = (new Query())
            ->from('system_flags')
            ->where(['key' => $key])
            ->one();

        if ($row && $row['value'] === $today) {
            return false; // already ran today
        }

        // ✅ Your logic here
        Yii::info("Running task...");
        BoatRegistrationController::markeAsExpired();
        HighseasLicenseController::markeAsExpired();
        NationalLicenseController::markeAsExpired();
        FishermanController::markeAsExpired();
        DepartureBoatController::markeAsAllowed();
        DepartureBoatController::markeAsServicepending();


        if ($row) {
            Yii::$app->db->createCommand()->update('system_flags', [
                'value' => $today
            ], ['key' => $key])->execute();
        } else {
            Yii::$app->db->createCommand()->insert('system_flags', [
                'key' => $key,
                'value' => $today
            ])->execute();
        }

        return true;
    }

    /**
 * Validate a Cloudflare Turnstile token.
 */
private function validateTurnstile(string $token): bool
{
    $token = trim($token);

    if ($token === '') {
        Yii::warning(
            'Turnstile validation failed: token is missing.',
            'security.turnstile'
        );

        return false;
    }

    $secretKey = Yii::$app->params['turnstile']['secretKey'] ?? '';

    if ($secretKey === '') {
        Yii::error(
            'Turnstile validation failed: secret key is not configured.',
            'security.turnstile'
        );

        return false;
    }

    if (!function_exists('curl_init')) {
        Yii::error(
            'Turnstile validation failed: PHP cURL is unavailable.',
            'security.turnstile'
        );

        return false;
    }

    $curl = curl_init(
        'https://challenges.cloudflare.com/turnstile/v0/siteverify'
    );

    if ($curl === false) {
        Yii::error(
            'Turnstile validation failed: unable to initialize cURL.',
            'security.turnstile'
        );

        return false;
    }

    curl_setopt_array($curl, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'secret' => $secretKey,
            'response' => $token,
            'remoteip' => Yii::$app->request->userIP,
        ]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/x-www-form-urlencoded',
        ],
    ]);

    $response = curl_exec($curl);
    $curlError = curl_error($curl);
    $httpStatus = (int) curl_getinfo(
        $curl,
        CURLINFO_HTTP_CODE
    );

    curl_close($curl);

    if ($response === false || $httpStatus !== 200) {
        Yii::warning(
            sprintf(
                'Turnstile Siteverify request failed. HTTP status: %d. Error: %s',
                $httpStatus,
                $curlError
            ),
            'security.turnstile'
        );

        return false;
    }

    try {
        $result = json_decode(
            $response,
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    } catch (\JsonException $exception) {
        Yii::warning(
            'Turnstile returned invalid JSON: '
            . $exception->getMessage(),
            'security.turnstile'
        );

        return false;
    }

    if (($result['success'] ?? false) !== true) {
        Yii::warning(
            'Turnstile rejected the token. Error codes: '
            . implode(
                ', ',
                $result['error-codes'] ?? ['unknown-error']
            ),
            'security.turnstile'
        );

        return false;
    }

    /*
     * Confirm that the token was generated for the login action.
     */
    if (($result['action'] ?? '') !== 'login') {
        Yii::warning(
            'Turnstile action mismatch.',
            'security.turnstile'
        );

        return false;
    }

    /*
     * Confirm that the token originated from an approved hostname.
     */
    $allowedHostnames = [
        'msdfar.com',
        'www.msdfar.com',
        'msdfar.lk',

        // Required only for local testing with Cloudflare test keys.
        'localhost',
        '127.0.0.1',
    ];

    $hostname = strtolower(
        (string) ($result['hostname'] ?? '')
    );

    if (!in_array($hostname, $allowedHostnames, true)) {
        Yii::warning(
            'Turnstile hostname mismatch: ' . $hostname,
            'security.turnstile'
        );

        return false;
    }

    return true;
}
}
