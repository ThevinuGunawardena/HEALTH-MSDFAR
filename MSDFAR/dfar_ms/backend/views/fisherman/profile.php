<?php

/** @var yii\web\View $this */

use backend\config\Constant;
use backend\models\FishermanRegisterdBoatLicense;
use backend\models\FishermanRegisterdBoat;
use backend\models\HighseasLicense;
use backend\models\NationalLicense;
use backend\models\SkipperRenew;
use backend\models\ProfileFishermanRenew;
use backend\services\CommonService;
use yii\helpers\Html;
use backend\components\SecurityHelper;
use backend\models\BoatNumbers;

$this->title = 'Fisherman Dashboard';
$webURL = Yii::getAlias('@web');

?>
<?php //Pjax::begin(['id' => 'boat-numbers']); ?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-3">
                    <div class="image-card">
                        <img src="<?=Constant::$FILE_VIEW_PATH?>fisherman/<?= $fisherMan->profile_image ?>">
                    </div>
                </div>
                <div class="col-lg-6">

                    <div class="detail-card">
                        <div class="details">
                            <label id="lbl"><?= Yii::t('app', 'Fisherman ID') ?></label>
                            <label id="ProfDetails"> <?= $fisherMan->fisherman_uid ?? "Not Generated" ?></label>
                        </div>
                        <div class="details">
                            <label id="lbl"><?= Yii::t('app', 'Name') ?></label>
                            <label id="ProfDetails"><?= $fisherMan->first_name ?> <?= $fisherMan->last_name ?></label>
                        </div>
                        <div class="details">
                            <label id="lbl"><?= Yii::t('app', 'Address') ?></label>
                            <label id="ProfDetails"><?= $fisherMan->current_address ?></label>
                        </div>
                        <div class="details">
                            <label id="lbl"><?= Yii::t('app', 'Mobile') ?></label>
                            <label id="ProfDetails"><?= $fisherMan->mobile ?></label>
                        </div>
                        <div class="details">
                            <label id="lbl"><?= Yii::t('app', 'Home') ?></label>
                            <label id="ProfDetails"><?= $fisherMan->fixed_line ?></label>
                        </div>
                    </div>
                    <a href="profile-edit" class=""><i class="fa fa-edit" aria-hidden="true"></i></a>
                </div>
                <div class="col-lg-3">
                    <div class="row" id="categoryName">
                        <div class="col">
                            <div class="category">
                              <h3>
    <?php if ($fisherMan->status == Constant::Pending): ?>

        <?= Yii::t('app', 'Approval Pending') ?>

            <?php else: ?>

                <?php
                $hasRenewal =
                    (int) $fisherMan->renew === 1
                    && !empty($fisherMan->renew_id);

                $licenseRoute = $hasRenewal
                    ? [
                        '/fisherman/renew-license-view',
                        'token' => SecurityHelper::encryptId(
                            \backend\models\ProfileFishermanRenew::class,
                            $fisherMan->renew_id
                        ),
                    ]
                    : [
                        '/fisherman/license-view',
                        'token' => SecurityHelper::encryptId(
                            \backend\models\ProfileFisherman::class,
                            $fisherMan->id
                        ),
                    ];
                ?>

                <?= Html::a(
                    Yii::t('app', 'Fisherman'),
                    $licenseRoute
                ) ?>

            <?php endif; ?>
            </h3>
                            </h3>
                            </div>
                        </div>

                    </div>
                    <?php if (isset($mySkipperLicence) && sizeof($mySkipperLicence) > 0) {
                        if ($mySkipperLicence[0]->status == Constant::Active) { ?>
                            <div class="row" id="categoryName">
                                <div class="col">
                                    <div class="category" style="margin-top: 5px;">
                                        <h3><a href="../skipper/license-view?id=<?= $mySkipperLicence[0]->id ?>">
                                                <?= Yii::t('app', 'Skipper') ?>  </a>
                                        </h3>
                                    </div>
                                </div>

                            </div>
                        <?php }
                    } ?>
                </div>

            </div>
        </div>
    </div>
</div>
<!--    <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12">-->
<!--        <div class="card mb-5 shadow-sm">-->
<!--            <div class="card-body">-->
<!--                <div class="row">-->
<!--                    <div class="col-xl-4">-->
<!--                        <h3>Family Details</h3>-->
<!---->
<!--                    </div>-->
<!--                    <div class="col-xl-6">-->
<!--                        <a href="fm-register3" class="btn btn-primary">Update</a><br><br>-->
<!---->
<!--                    </div>-->
<!---->
<!--                </div>-->
<!--                <div class="">-->
<!--                    <table class="table table-striped">-->
<!--                        <thead>-->
<!--                        <tr>-->
<!--                            <th scope="col">#</th>-->
<!--                            <th scope="col"></th>-->
<!--                            <th scope="col">Dependent Name</th>-->
<!--                            <th scope="col"></th>-->
<!--                            <th scope="col">Dependent Relation</th>-->
<!--                            <th scope="col"></th>-->
<!--                            <th scope="col">Dependent NIC</th>-->
<!---->
<!--                        </tr>-->
<!--                        </thead>-->
<!--                        <tbody>-->
<!--                        <tr>-->
<!--                            <th scope="row">1</th>-->
<!--                            <td></td>-->
<!--                            <td>Mrs. Nimali Vidarshani</td>-->
<!--                            <td></td>-->
<!--                            <td>Wife</td>-->
<!--                            <td></td>-->
<!--                            <td>19685474785</td>-->
<!---->
<!---->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <th scope="row">2</th>-->
<!--                            <td></td>-->
<!--                            <td>Mr. Kasun Kumara</td>-->
<!--                            <td></td>-->
<!--                            <td>Son</td>-->
<!--                            <td></td>-->
<!--                            <td>200007574784</td>-->
<!---->
<!--                        </tr>-->
<!---->
<!--                        </tbody>-->
<!--                    </table>-->
<!--                </div>-->
<!--            </div>-->
<!---->
<!--        </div>-->
<!---->
<!---->
<!--    </div>-->
                            <?php if ($fisherMan->status != Constant::Pending) { ?>

                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="card mb-5 shadow-sm">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <h3><?= Yii::t('app', 'My Licences') ?></h3>
                                                </div>
                                                <div class="col-lg-12">
                                                    <table class="table table-striped">
                                                        <thead>
                                                        <tr>
                                                            <th scope="col" colspan="2"><?= Yii::t('app', 'Number') ?></th>
                                                            <th><?= Yii::t('app', 'Category') ?></th>
                                                            <th><?= Yii::t('app', 'Status') ?></th>
                                                            <th><?= Yii::t('app', 'Expire Date') ?></th>
                                                            <th scope="col" colspan="2"><?= Yii::t('app', 'Action') ?></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                     <?php foreach ($myFishermanLicense as $fishermanLicense): ?>

    <?php
    $action = '';
    $expireNotices = '';

    /*
     * Find all renewals belonging to this original fisherman licence.
     * The controller result is expected to be ordered by ID descending,
     * so the first result is the latest renewal.
     */
    $renewalLicenses = array_values(
        array_filter(
            $myFishermanRenewalLicenses,
            static function (
                $renewalLicense
            ) use (
                $fishermanLicense
            ): bool {
                return (string) $renewalLicense->fisherman_id
                    === (string) $fishermanLicense->id;
            }
        )
    );

    $latestRenewal = $renewalLicenses[0] ?? null;
    $hasRenewal = $latestRenewal !== null;

    /*
     * If a renewal exists, the original row opens the latest renewal.
     * Otherwise, it opens the original licence.
     */
    $viewLicenseRoute = $hasRenewal
    ? [
        '/fisherman/renew-license-view',
        'token' => SecurityHelper::encryptId(
            \backend\models\ProfileFishermanRenew::class,
            $latestRenewal->id
        ),
    ]
    : [
        '/fisherman/license-view',
        'token' => SecurityHelper::encryptId(
            \backend\models\ProfileFisherman::class,
            $fishermanLicense->id
        ),
    ];

    if ($fishermanLicense->status == Constant::Active) {
        $action = Html::a(
            Yii::t('app', 'View License'),
            $viewLicenseRoute,
            [
                'class' => 'btn btn-primary btn-sm',
            ]
        );
    }

    /*
     * actionRenew() loads a Fisherman record, so the encrypted token
     * must be bound to Fisherman::class.
     */
    $renewRoute = [
        '/fisherman/renew',
        'token' => SecurityHelper::encryptId(
            Fisherman::class,
            $fishermanLicense->id
        ),
    ];

    /*
     * Show Renew Now on the original licence only when there are
     * no renewal records yet.
     */
    if (
        !$hasRenewal
        && $fishermanLicense->status == Constant::Active
    ) {
        $dateDifference =
            CommonService::getDateDiffWithCurrentDate(
                $fishermanLicense->expire_date
            );

        if ($dateDifference <= 30) {
            $expireNotices .= Html::tag(
                'span',
                Yii::t(
                    'app',
                    'Expires in {days} day(s)',
                    [
                        'days' => $dateDifference,
                    ]
                ),
                [
                    'class' => 'badge badge-warning',
                ]
            );

            $action .= ' ' . Html::a(
                Yii::t('app', 'Renew Now'),
                $renewRoute,
                [
                    'class' =>
                        'btn btn-warning btn-sm national-license-view',
                ]
            );
        }
    }

    if (
        !$hasRenewal
        && $fishermanLicense->status == Constant::Expired
    ) {
        $dateDifference =
            CommonService::getDateDiffWithCurrentDate(
                $fishermanLicense->expire_date
            );

        $expireNotices .= Html::tag(
            'span',
            Yii::t(
                'app',
                'Expired {days} day(s) ago',
                [
                    'days' => abs($dateDifference),
                ]
            ),
            [
                'class' => 'badge badge-warning',
            ]
        );

        $action .= ' ' . Html::a(
            Yii::t('app', 'Renew Now'),
            $renewRoute,
            [
                'class' =>
                    'btn btn-warning btn-sm national-license-view',
            ]
        );
    }

    $statusLabel =
        Constant::$licenseStatus[
            $fishermanLicense->status
        ] ?? '';

    $status = Html::tag(
        'span',
        Html::encode($statusLabel)
    );
    ?>

    <!-- Original Fisherman Licence Row -->
    <tr>
        <td colspan="2">
            <?= Html::encode(
                $fishermanLicense->fisherman_uid
            ) ?>
        </td>

        <td>
            <?= Yii::t('app', 'Fisherman') ?>
        </td>

        <td>
            <?= $status ?>
        </td>

        <td>
            <?= Html::encode(
                $fishermanLicense->expire_date ?? 'NA'
            ) ?>

            <?= $expireNotices ?>
        </td>

        <td>
            <?= $action ?>
        </td>
    </tr>

    <!-- Renewal Licence Rows -->
    <?php foreach ($renewalLicenses as $renewalLicense): ?>

        <?php
        $renewalViewRoute = [
            '/fisherman/renew-license-view',
            'token' => SecurityHelper::encryptId(
                ProfileFishermanRenew::class,
                $renewalLicense->id
            ),
        ];

        $renewalAction = Html::a(
            Yii::t('app', 'View License'),
            $renewalViewRoute,
            [
                'class' => 'btn btn-primary btn-sm',
            ]
        );

        $renewalExpireNotice = '';

        $renewalDateDifference =
            !empty($renewalLicense->expire_date)
                ? CommonService::getDateDiffWithCurrentDate(
                    $renewalLicense->expire_date
                )
                : null;

        /*
         * Only the latest renewal may initiate another renewal.
         */
        $isLatestRenewal =
            $latestRenewal !== null
            && (int) $latestRenewal->id
                === (int) $renewalLicense->id;

        $renewalIsExpired =
            $renewalLicense->status == Constant::Expired
            || (
                $renewalDateDifference !== null
                && $renewalDateDifference < 0
            );

        $renewalNearExpiry =
            $renewalLicense->status == Constant::Active
            && $renewalDateDifference !== null
            && $renewalDateDifference >= 0
            && $renewalDateDifference <= 30;

        if ($renewalIsExpired) {
            $renewalExpireNotice = Html::tag(
                'span',
                Yii::t(
                    'app',
                    'Expired {days} day(s) ago',
                    [
                        'days' => abs(
                            $renewalDateDifference ?? 0
                        ),
                    ]
                ),
                [
                    'class' => 'badge badge-warning',
                ]
            );
        } elseif ($renewalNearExpiry) {
            $renewalExpireNotice = Html::tag(
                'span',
                Yii::t(
                    'app',
                    'Expires in {days} day(s)',
                    [
                        'days' => $renewalDateDifference,
                    ]
                ),
                [
                    'class' => 'badge badge-warning',
                ]
            );
        }

        /*
         * Only the latest expired or near-expiry renewal can create
         * another renewal request.
         */
        if (
            $isLatestRenewal
            && (
                $renewalIsExpired
                || $renewalNearExpiry
            )
        ) {
            $renewalAction .= ' ' . Html::a(
                Yii::t('app', 'Renew Now'),
                $renewRoute,
                [
                    'class' =>
                        'btn btn-warning btn-sm national-license-view',
                ]
            );
        }

        $renewalStatusLabel =
            Constant::$licenseStatus[
                $renewalLicense->status
            ] ?? '';

        $renewalStatus = Html::tag(
            'span',
            Html::encode($renewalStatusLabel)
        );
        ?>

        <tr>
            <td colspan="2">
                <?= Html::encode(
                    $fishermanLicense->fisherman_uid
                ) ?>
            </td>

            <td>
                <?= Yii::t(
                    'app',
                    'Fisherman (Renew)'
                ) ?>
            </td>

            <td>
                <?= $renewalStatus ?>
            </td>

            <td>
                <?= Html::encode(
                    $renewalLicense->expire_date ?? 'NA'
                ) ?>

                <?= $renewalExpireNotice ?>
            </td>

            <td>
                <?= $renewalAction ?>
            </td>
        </tr>

    <?php endforeach; ?>

<?php endforeach; ?>

       <?php foreach ($mySkipperLicence as $skipperLicence): ?>

    <?php
    $action = '';
    $expireNotices = '';

    /*
     * Resolve the original Skipper record.
     *
     * Do not directly trust the ID from the collection because
     * the collection may contain a SkipperRenew model or joined data.
     */
    $originalSkipper = null;

    if ($skipperLicence instanceof \backend\models\Skipper) {
        $originalSkipper = $skipperLicence;
    } else {
        $skipperUid = trim(
            (string) (
                $skipperLicence->skipper_uid
                ?? ''
            )
        );

        if ($skipperUid !== '') {
            $originalSkipper = \backend\models\Skipper::find()
                ->where([
                    'skipper_uid' => $skipperUid,
                ])
                ->orderBy([
                    'id' => SORT_ASC,
                ])
                ->one();
        }
    }

    /*
     * Do not generate a token unless the original Skipper
     * record actually exists.
     */
    if ($originalSkipper === null) {
        Yii::warning(
            [
                'message' =>
                    'Original Skipper record was not found.',
                'receivedClass' =>
                    is_object($skipperLicence)
                        ? get_class($skipperLicence)
                        : gettype($skipperLicence),
                'receivedId' =>
                    $skipperLicence->id ?? null,
                'skipperUid' =>
                    $skipperLicence->skipper_uid ?? null,
            ],
            'skipper-renew-token'
        );

        continue;
    }

    $originalSkipperId = (int) (
        $originalSkipper->id ?? 0
    );

    if ($originalSkipperId <= 0) {
        continue;
    }

    /*
     * Use the fully qualified class name.
     *
     * This avoids Skipper::class resolving to the wrong namespace
     * when the import is missing or incorrect.
     */
    $skipperToken =
        \backend\components\SecurityHelper::encryptId(
            \backend\models\Skipper::class,
            $originalSkipperId
        );

    /*
     * Check whether a renewal request is already in progress.
     */
    $renewExists = \backend\models\SkipperRenew::find()
        ->where([
            'skipper_uid' =>
                $originalSkipper->skipper_uid,
        ])
        ->andWhere([
            'status' => [
                Constant::Pending,
                Constant::PaymentPending,
                Constant::FinalApprovalPending,
            ],
        ])
        ->exists();

    $skipperStatus = (int) (
        $originalSkipper->status ?? 0
    );

    /*
     * Default status.
     */
    $statusText =
        Constant::$licenseStatus[$skipperStatus]
        ?? Yii::t('app', 'Unknown');

    $status = Html::tag(
        'span',
        Yii::t('app', $statusText),
        [
            'class' => 'badge badge-secondary',
        ]
    );

    /*
     * Payment Pending.
     */
    if (
        $skipperStatus
        === (int) Constant::PaymentPending
    ) {
        $status = Html::tag(
            'span',
            Yii::t('app', 'To be Paid'),
            [
                'class' => 'badge badge-warning',
            ]
        );

        $action = Html::a(
            Yii::t('app', 'Pay Now'),
            [
                '/skipper/payment',
                'token' => $skipperToken,
            ],
            [
                'class' =>
                    'btn btn-warning btn-sm mb-1',
            ]
        );
    }

    /*
     * Final Approval Pending.
     */
    if (
        $skipperStatus
        === (int) Constant::FinalApprovalPending
    ) {
        $status = Html::tag(
            'span',
            Yii::t(
                'app',
                'Waiting for final approval'
            ),
            [
                'class' => 'badge badge-success',
            ]
        );
    }

    /*
     * Active licence.
     */
    if (
        $skipperStatus
        === (int) Constant::Active
    ) {
        $status = Html::tag(
            'span',
            Yii::t('app', 'Active'),
            [
                'class' => 'badge badge-info',
            ]
        );

        $action = Html::a(
            Yii::t('app', 'View License'),
            [
                '/skipper/license-view',
                'token' => $skipperToken,
            ],
            [
                'class' =>
                    'btn btn-primary btn-sm mb-1',
            ]
        );

        if (!empty($originalSkipper->expire_date)) {
            $dateDiff = (int) (
                CommonService::getDateDiffWithCurrentDate(
                    $originalSkipper->expire_date
                )
            );

            /*
             * Permit renewal during the final 30 days
             * and after expiration.
             */
            if ($dateDiff <= 30) {
                if ($dateDiff < 0) {
                    $expireMessage = Yii::t(
                        'app',
                        'Expired {days} day(s) ago',
                        [
                            'days' =>
                                abs($dateDiff),
                        ]
                    );
                } elseif ($dateDiff === 0) {
                    $expireMessage = Yii::t(
                        'app',
                        'Expiring today'
                    );
                } else {
                    $expireMessage = Yii::t(
                        'app',
                        'Expiring in {days} day(s)',
                        [
                            'days' => $dateDiff,
                        ]
                    );
                }

                $expireNotices = Html::tag(
                    'span',
                    $expireMessage,
                    [
                        'class' =>
                            'badge badge-warning ml-1',
                    ]
                );

                if (!$renewExists) {
                    $action .= ' ';

                    $action .= Html::a(
                        Yii::t(
                            'app',
                            'Renew Now'
                        ),
                        [
                            '/skipper/renew',
                            'token' =>
                                $skipperToken,
                        ],
                        [
                            'class' =>
                                'btn btn-warning btn-sm mb-1 skipper-license-renew',
                        ]
                    );
                } else {
                    $action .= ' ';

                    $action .= Html::tag(
                        'span',
                        Yii::t(
                            'app',
                            'Renewal already submitted'
                        ),
                        [
                            'class' =>
                                'badge badge-info',
                        ]
                    );
                }
            }
        }
    }

    /*
     * Expired licence.
     */
    if (
        $skipperStatus
        === (int) Constant::Expired
    ) {
        $status = Html::tag(
            'span',
            Yii::t('app', 'Expired'),
            [
                'class' => 'badge badge-danger',
            ]
        );

        if (!empty($originalSkipper->expire_date)) {
            $dateDiff = (int) (
                CommonService::getDateDiffWithCurrentDate(
                    $originalSkipper->expire_date
                )
            );

            $expireNotices = Html::tag(
                'span',
                Yii::t(
                    'app',
                    'Expired {days} day(s) ago',
                    [
                        'days' =>
                            abs($dateDiff),
                    ]
                ),
                [
                    'class' =>
                        'badge badge-warning ml-1',
                ]
            );
        }

        if (!$renewExists) {
            $action = Html::a(
                Yii::t(
                    'app',
                    'Renew Now'
                ),
                [
                    '/skipper/renew',
                    'token' => $skipperToken,
                ],
                [
                    'class' =>
                        'btn btn-warning btn-sm mb-1 skipper-license-renew',
                ]
            );
        } else {
            $action = Html::tag(
                'span',
                Yii::t(
                    'app',
                    'Renewal already submitted'
                ),
                [
                    'class' => 'badge badge-info',
                ]
            );
        }
    }

    $expireDate = !empty(
        $originalSkipper->expire_date
    )
        ? date(
            'Y-m-d',
            strtotime(
                $originalSkipper->expire_date
            )
        )
        : Yii::t('app', 'N/A');
    ?>

    <tr>
        <td colspan="2">
            <?= Html::encode(
                (string) (
                    $originalSkipper->skipper_uid
                    ?? ''
                )
            ) ?>
        </td>

        <td>
            <?= Html::encode(
                Yii::t('app', 'Skipper')
            ) ?>
        </td>

        <td>
            <?= $status ?>
        </td>

        <td>
            <?= Html::encode($expireDate) ?>

            <?= $expireNotices ?>
        </td>

        <td>
            <?= $action ?>
        </td>
    </tr>

<?php endforeach; ?>
                            <?php

                            foreach ($myNationalLicence as $nationalLicence) {
                                /*
                                * Check whether a newer renewal record already exists.
                                */
                                $hasRenewal = NationalLicense::find()
                                    ->where([
                                        'boat_registration_id' =>
                                            $nationalLicence->boat_registration_id,
                                        'renew' => 1,
                                    ])
                                    ->andWhere([
                                        '>',
                                        'id',
                                        $nationalLicence->id,
                                    ])
                                    ->exists();

                                $actionButtons = [];
                                $expireNotices = [];

                                $statusText = Constant::$licenseStatus[
                                    $nationalLicence->status
                                ] ?? 'Unknown';

                                /*
                                * Active licence actions.
                                */
                                if (
                                    (int) $nationalLicence->status
                                    === (int) Constant::Active
                                ) {
                                    $actionButtons[] = Html::a(
                                        Yii::t('app', 'View License'),
                                        [
                                            '/national-license/license-view',
                                            'token' => SecurityHelper::encryptId(
                                                \backend\models\NationalLicense::class,
                                                $nationalLicence->id
                                            ),
                                        ],
                                        [
                                            'class' =>
                                                'btn btn-primary btn-sm national-license-view',
                                        ]
                                    );

                                    $dateDifference =
                                        CommonService::getDateDiffWithCurrentDate(
                                            $nationalLicence->expire_date
                                        );

                                    /*
                                    * Show renewal option during the final 30 days.
                                    */
                                    if ($dateDifference <= 30) {
                                        if ($dateDifference <= 0) {
                                            $expireNotices[] = Html::tag(
                                                'span',
                                                Yii::t(
                                                    'app',
                                                    'Expired {days} days ago',
                                                    [
                                                        'days' => abs(
                                                            (int) $dateDifference
                                                        ),
                                                    ]
                                                ),
                                                [
                                                    'class' => 'badge badge-warning',
                                                ]
                                            );
                                        }

                                        if (!$hasRenewal) {
                                            $actionButtons[] = Html::a(
                                                Yii::t('app', 'Renew Now'),
                                                [
                                                    '/national-license/renew',
                                                    'token' => SecurityHelper::encryptId(
                                                        \backend\models\NationalLicense::class,
                                                        $nationalLicence->id
                                                    ),
                                                ],
                                                [
                                                    'class' =>
                                                        'btn btn-warning btn-sm national-license-view',
                                                ]
                                            );
                                        }
                                    }
                                }

                                /*
                                * Expired licence actions.
                                */
                                if (
                                    (int) $nationalLicence->status
                                    === (int) Constant::Expired
                                ) {
                                    $dateDifference =
                                        CommonService::getDateDiffWithCurrentDate(
                                            $nationalLicence->expire_date
                                        );

                                    if ($dateDifference <= 0) {
                                        $expireNotices[] = Html::tag(
                                            'span',
                                            Yii::t(
                                                'app',
                                                'Expired {days} days ago',
                                                [
                                                    'days' => abs(
                                                        (int) $dateDifference
                                                    ),
                                                ]
                                            ),
                                            [
                                                'class' => 'badge badge-warning',
                                            ]
                                        );

                                        if (!$hasRenewal) {
                                            $actionButtons[] = Html::a(
                                                Yii::t('app', 'Renew Now'),
                                                [
                                                    '/national-license/renew',
                                                    'token' => SecurityHelper::encryptId(
                                                        \backend\models\NationalLicense::class,
                                                        $nationalLicence->id
                                                    ),
                                                ],
                                                [
                                                    'class' =>
                                                        'btn btn-warning btn-sm national-license-view',
                                                ]
                                            );
                                        }
                                    }
                                }

                                $statusHtml = Html::tag(
                                    'span',
                                    Yii::t('app', $statusText)
                                );

                                $actionHtml = implode(' ', $actionButtons);
                                $expireNoticeHtml = implode(' ', $expireNotices);
                                ?>

                                <tr>
                                    <td colspan="2">
                                        <?= Html::encode(
                                            $nationalLicence->license_number
                                                ?? Yii::t('app', 'Not Generated')
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= Html::encode(
                                            Yii::t('app', 'National License')
                                        ) ?>

                                        <br>

                                        <?= Html::encode(
                                            Yii::t('app', 'Boat number')
                                        ) ?>:

                                        <?= Html::encode(
                                            $nationalLicence
                                                ->boatRegistration
                                                ?->boatNumber
                                                ?->boat_number
                                            ?? Yii::t('app', 'Not Available')
                                        ) ?>

                                        <?php if ((int) $nationalLicence->renew === 1): ?>
                                            <?= Html::encode(
                                                Yii::t('app', '(Renew)')
                                            ) ?>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?= $statusHtml ?>
                                    </td>

                                    <td>
                                        <?= Html::encode(
                                            $nationalLicence->expire_date ?? 'NA'
                                        ) ?>

                                        <?= $expireNoticeHtml ?>
                                    </td>

                                    <td>
                                        <?= $actionHtml ?>
                                    </td>
                                </tr>

                                <?php
                            }
                            ?>
                            <?php foreach ($myHighseasLicence as $highseasLicence): ?>

    <?php
    /*
     * Check whether a later renewal request already exists
     * for this specific licence/boat owner combination.
     */
    $hasRenewalRequest = HighseasLicense::find()
        ->where([
            'boat_registration_id' =>
                $highseasLicence->boat_registration_id,
            'fisherman_id' =>
                $highseasLicence->fisherman_id,
            'renew' => 1,
        ])
        ->andWhere([
            '>',
            'id',
            $highseasLicence->id,
        ])
        ->exists();

    /*
     * Generate one encrypted token and reuse it for all
     * High Seas licence actions.
     */
    $licenseToken = SecurityHelper::encryptId(
        HighseasLicense::class,
        $highseasLicence->id
    );

    $action = '';
    $expireNotices = '';

    $licenseStatus = Constant::$licenseStatus[
        $highseasLicence->status
    ] ?? Yii::t('app', 'Unknown');

    /*
     * Payment-pending action.
     *
     * When this is enabled, send the encrypted token instead
     * of exposing the numeric licence ID.
     */
    if ((int) $highseasLicence->status === 99) {
        /*
        $action = Html::button(
            Yii::t('app', 'Pay Now'),
            [
                'class' =>
                    'btn btn-primary btn-sm pay-highseas-licence',
                'data-token' => $licenseToken,
                'type' => 'button',
            ]
        );
        */
    }

    if (
        (int) $highseasLicence->status
        === (int) Constant::Active
    ) {
        $action = Html::a(
            Yii::t('app', 'View License'),
            [
                '/highseas-license/license-view',
                'token' => $licenseToken,
            ],
            [
                'class' => 'btn btn-primary btn-sm',
            ]
        );

        $datediff = CommonService::getDateDiffWithCurrentDate(
            $highseasLicence->expire_date
        );

        if ($datediff <= 60) {
            if ($datediff <= 0) {
                $expireNotices = Html::tag(
                    'span',
                    Yii::t(
                        'app',
                        'Expired {days} days ago',
                        [
                            'days' => abs((int) $datediff),
                        ]
                    ),
                    [
                        'class' => 'badge badge-warning',
                    ]
                );
            } else {
                $expireNotices = Html::tag(
                    'span',
                    Yii::t(
                        'app',
                        'Expires in {days} days',
                        [
                            'days' => (int) $datediff,
                        ]
                    ),
                    [
                        'class' => 'badge badge-warning',
                    ]
                );
            }

            if (!$hasRenewalRequest) {
                $action .= ' ' . Html::a(
                    Yii::t('app', 'Renew Now'),
                    [
                        '/highseas-license/renew',
                        'token' => $licenseToken,
                    ],
                    [
                        'class' =>
                            'btn btn-warning btn-sm',
                    ]
                );
            }
        }
    }

    if (
        (int) $highseasLicence->status
        === (int) Constant::Expired
    ) {
        $datediff = CommonService::getDateDiffWithCurrentDate(
            $highseasLicence->expire_date
        );

        if (
            $datediff <= 0
            && !$hasRenewalRequest
        ) {
            $expireNotices = Html::tag(
                'span',
                Yii::t(
                    'app',
                    'Expired {days} days ago',
                    [
                        'days' => abs((int) $datediff),
                    ]
                ),
                [
                    'class' => 'badge badge-warning',
                ]
            );

            $action = Html::a(
                Yii::t('app', 'Renew Now'),
                [
                    '/highseas-license/renew',
                    'token' => $licenseToken,
                ],
                [
                    'class' =>
                        'btn btn-warning btn-sm highseas-license-renew',
                ]
            );
        }
    }

    $status = Html::tag(
        'span',
        Yii::t('app', $licenseStatus)
    );

    $boatNumber = $highseasLicence
        ->boatRegistration
        ?->boatNumber
        ?->boat_number ?? '';

    $licenseNumber =
        $highseasLicence->license_number
        ?: Yii::t('app', 'Not Generated');

    $expiryDate =
        $highseasLicence->expire_date
        ?: Yii::t('app', 'NA');
    ?>

    <tr>
        <td colspan="2">
            <?= Html::encode($licenseNumber) ?>
        </td>

        <td>
            <?= Html::encode(
                Yii::t('app', 'Highseas License')
            ) ?>

            <br>

            <?= Html::encode(
                Yii::t('app', 'Boat number')
            ) ?>:

            <?= Html::encode($boatNumber) ?>

            <?php if (
                (int) $highseasLicence->renew === 1
            ): ?>
                <?= Html::encode(
                    Yii::t('app', '(Renew)')
                ) ?>
            <?php endif; ?>
        </td>

        <td>
            <?= $status ?>
        </td>

        <td>
            <?= Html::encode($expiryDate) ?>

            <?= $expireNotices ?>
        </td>

        <td>
            <?= $action ?>
        </td>
    </tr>

<?php endforeach; ?>
                            <!--                                --><?php
                            //                                foreach ($myYardLicence as $yardLicence) {
                            //                                    $action = "";
                            //                                    $status = '';
                            //                                    $expireNotices = '';
                            //                                    if ($yardLicence->status == 99) {
                            //                                        $action = '<button  class="btn btn-primary btn-sm pay-yard-licence" data-yard="' . $yardLicence->id . '">Pay Now</button>';
                            //                                    }
                            //
                            //                                    if ($yardLicence->status == Constant::Active) {
                            ////                                    $status = '<span class="badge badge-info"><?=$myBoatNumber->Active</span>';
                            //                                        $action = '<a  href="../yard/license-view?id=' . $yardLicence->id . '" class="btn btn-primary btn-sm">View License</a>';
                            //                                        $datediff = CommonService::getDateDiffWithCurrentDate($yardLicence->expire_date);
                            //                                        if ($datediff <= 0) {
                            //                                            $expireNotices .= '<span class="badge badge-warning"><?=$myBoatNumber->Expired ' . $datediff . ' Days ago</span>';
                            //                                            $action .= ' <button  class="btn btn-warning btn-sm national-license-view" data-national="' . $yardLicence->id . '">Renew Now</button>';
                            //
                            //                                        }
                            //                                    }
                            //
                            //
                            //                                    $status .= '<span class=""><?=$myBoatNumber->' . Constant::$licenseStatus[$yardLicence->status] . '</span> ';
                            //
                            //                                    ?>
                            <!--                                    <tr>-->
                            <!--                                        <td colspan="2">-->
                            <?php //= $yardLicence->name ?><!--</td>-->
                            <!--                                        <td>Yard License</td>-->
                            <!--                                        <td>--><?php //= $status ?><!--</td>-->
                            <!--                                        <td>-->
                            <?php //= $yardLicence->expire_date ?? "NA" ?><!-- -->
                            <?php //= $expireNotices ?><!--</td>-->
                            <!--                                        <td>--><?php //= $action ?><!--</td>-->
                            <!--                                    </tr>-->
                            <!--                                    --><?php
                            //                                }
                            //                                ?>


                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>

    </div>

   <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card mb-5 shadow-sm">

        <div class="card-body">

            <h3>
                <?= Html::encode(
                    Yii::t(
                        'app',
                        'Boat Number Details'
                    )
                ) ?>
            </h3>

            <div class="row">

                <div class="col-lg-12">

                    <div class="table-responsive">

                        <table class="table table-striped">

                            <thead>
                            <tr>
                                <th
                                    scope="col"
                                    colspan="2"
                                >
                                    <?= Html::encode(
                                        Yii::t(
                                            'app',
                                            'Boat Number'
                                        )
                                    ) ?>
                                </th>

                                <th scope="col">
                                    <?= Html::encode(
                                        Yii::t(
                                            'app',
                                            'Status'
                                        )
                                    ) ?>
                                </th>

                                <th scope="col">
                                    <?= Html::encode(
                                        Yii::t(
                                            'app',
                                            'Action'
                                        )
                                    ) ?>
                                </th>
                            </tr>
                            </thead>

                            <tbody>

                            <?php foreach (
                                $myBoatNumbers
                                as $myBoatNumber
                            ): ?>

                                <?php
                                $action = '';

                                /*
                                 * Resolve an actual BoatNumbers model before
                                 * generating a model-bound token.
                                 */
                                $boatNumberRecord = null;

                                if (
                                    $myBoatNumber
                                    instanceof BoatNumbers
                                ) {
                                    $boatNumberRecord =
                                        $myBoatNumber;
                                } else {
                                    $candidateId = (int) (
                                        $myBoatNumber->id
                                        ?? 0
                                    );

                                    if ($candidateId > 0) {
                                        $boatNumberRecord =
                                            BoatNumbers::findOne([
                                                'id' =>
                                                    $candidateId,
                                            ]);
                                    }

                                    /*
                                     * Fallback for joined or mapped records
                                     * where the returned ID is not
                                     * boat_numbers.id.
                                     */
                                    if (
                                        $boatNumberRecord === null
                                        && !empty(
                                            $myBoatNumber
                                                ->boat_number
                                        )
                                    ) {
                                        $boatNumberValue = trim(
                                            (string) (
                                                $myBoatNumber
                                                    ->boat_number
                                                ?? ''
                                            )
                                        );

                                        if (
                                            $boatNumberValue
                                            !== ''
                                        ) {
                                            $boatNumberRecord =
                                                BoatNumbers::find()
                                                    ->where([
                                                        'boat_number' =>
                                                            $boatNumberValue,
                                                    ])
                                                    ->one();
                                        }
                                    }
                                }

                                $boatNumberText = trim(
                                    (string) (
                                        $boatNumberRecord
                                            ->boat_number
                                        ?? $myBoatNumber
                                            ->boat_number
                                        ?? ''
                                    )
                                );

                                $boatStatus = (int) (
                                    $boatNumberRecord->status
                                    ?? $myBoatNumber->status
                                    ?? 0
                                );

                                $statusText =
                                    Constant::$licenseStatus[
                                        $boatStatus
                                    ]
                                    ?? Yii::t(
                                        'app',
                                        'Unknown'
                                    );

                                /*
                                 * Html::tag() receives raw content, so encode
                                 * the translated status text explicitly.
                                 */
                                $status = Html::tag(
                                    'span',
                                    Html::encode(
                                        Yii::t(
                                            'app',
                                            (string) $statusText
                                        )
                                    ),
                                    [
                                        'class' =>
                                            'badge badge-secondary',
                                    ]
                                );

                                if (
                                    $boatStatus ===
                                    (int) Constant::Active
                                ) {
                                    $status = Html::tag(
                                        'span',
                                        Html::encode(
                                            Yii::t(
                                                'app',
                                                'Active'
                                            )
                                        ),
                                        [
                                            'class' =>
                                                'badge badge-success',
                                        ]
                                    );
                                } elseif (
                                    $boatStatus ===
                                    (int) Constant::Pending
                                ) {
                                    $status = Html::tag(
                                        'span',
                                        Html::encode(
                                            Yii::t(
                                                'app',
                                                'Pending'
                                            )
                                        ),
                                        [
                                            'class' =>
                                                'badge badge-warning',
                                        ]
                                    );
                                } elseif (
                                    $boatStatus ===
                                    (int) Constant::Expired
                                ) {
                                    $status = Html::tag(
                                        'span',
                                        Html::encode(
                                            Yii::t(
                                                'app',
                                                'Expired'
                                            )
                                        ),
                                        [
                                            'class' =>
                                                'badge badge-danger',
                                        ]
                                    );
                                }

                                /*
                                 * Only create protected links when the actual
                                 * BoatNumbers record exists.
                                 */
                                if (
                                    $boatNumberRecord !== null
                                    && (int) $boatNumberRecord->id > 0
                                ) {
                                    $boatNumberToken =
                                        SecurityHelper::encryptId(
                                            BoatNumbers::class,
                                            (int)
                                                $boatNumberRecord
                                                    ->id
                                        );

                                    if (
                                        $boatStatus ===
                                        (int) Constant::Active
                                    ) {
                                        /*
                                         * Create registration only when there
                                         * is no registered-boat record.
                                         */
                                        $registeredBoats =
                                            $boatNumberRecord
                                                ->fishermanRegisterdBoats
                                            ?? [];

                                        if (
                                            empty($registeredBoats)
                                        ) {
                                            $action .= Html::a(
                                                Yii::t(
                                                    'app',
                                                    'Boat Registration'
                                                ),
                                                [
                                                    '/boat-registration/create',

                                                    /*
                                                     * Keep the parameter name
                                                     * "boat" if the receiving
                                                     * action already expects it.
                                                     * Its value is encrypted.
                                                     */
                                                    'boat' =>
                                                        $boatNumberToken,
                                                ],
                                                [
                                                    'class' =>
                                                        'btn btn-primary btn-sm mb-1',
                                                ]
                                            );
                                        }

                                        if ($action !== '') {
                                            $action .= ' ';
                                        }

                                        /*
                                         * Tokenized Boat Number licence link.
                                         */
                                        $action .= Html::a(
                                            Yii::t(
                                                'app',
                                                'View License'
                                            ),
                                            [
                                                '/boat-numbers/license-view',
                                                'token' =>
                                                    $boatNumberToken,
                                            ],
                                            [
                                                'class' =>
                                                    'btn btn-info btn-sm mb-1',
                                            ]
                                        );

                                        $action .= ' ';

                                        /*
                                         * Tokenized Boat Cancel link.
                                         *
                                         * BoatCancelController::actionCreate()
                                         * must decrypt this with
                                         * BoatNumbers::class.
                                         */
                                        $action .= Html::a(
                                            Yii::t(
                                                'app',
                                                'Cancel Boat'
                                            ),
                                            [
                                                '/boat-cancel/create',
                                                'token' =>
                                                    $boatNumberToken,
                                            ],
                                            [
                                            'class' =>
                                                'btn btn-danger btn-sm mb-1',
                                            'data' => [
                                                'confirm' => Yii::t(
                                                    'app',
                                                    'Are you sure you want to start the boat cancellation process?'
                                                ),
                                            ],
                                        ]
                                        );
                                    }
                                } else {
                                    Yii::warning(
                                        [
                                            'message' =>
                                                'Unable to resolve BoatNumbers record for dashboard row.',
                                            'receivedClass' =>
                                                is_object(
                                                    $myBoatNumber
                                                )
                                                    ? get_class(
                                                        $myBoatNumber
                                                    )
                                                    : gettype(
                                                        $myBoatNumber
                                                    ),
                                            'receivedId' =>
                                                $myBoatNumber->id
                                                ?? null,
                                            'boatNumber' =>
                                                $myBoatNumber
                                                    ->boat_number
                                                ?? null,
                                        ],
                                        'boat-number-dashboard'
                                    );
                                }
                                ?>

                                <tr>

                                    <td colspan="2">
                                        <?= Html::encode(
                                            $boatNumberText
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= $status ?>
                                    </td>

                                    <td>
                                        <?php if (
                                            $action !== ''
                                        ): ?>
                                            <?= $action ?>
                                        <?php else: ?>
                                            <span
                                                class="text-muted"
                                            >
                                                <?= Html::encode(
                                                    Yii::t(
                                                        'app',
                                                        'No action available'
                                                    )
                                                ) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                            <?php if (
                                empty($myBoatNumbers)
                            ): ?>

                                <tr>
                                    <td
                                        colspan="4"
                                        class="text-center text-muted"
                                    >
                                        <?= Html::encode(
                                            Yii::t(
                                                'app',
                                                'No boat numbers were found.'
                                            )
                                        ) ?>
                                    </td>
                                </tr>

                            <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>


</div>
    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <h3><?= Yii::t('app', 'Boat Cancellation Request') ?></h3>
                <div class="row">
                    <div class="col-lg-12">

                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <!--                                <th scope="col">#</th>-->
                                <th scope="col" colspan="2"><?= Yii::t('app', 'Boat Number') ?></th>
                                <th> <?= Yii::t('app', 'Status') ?></th>
                                <th><?= Yii::t('app', 'Action') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($myBoatCancelRequests as $cancelRequest) {
                                $status = '<span class=""><?=$myBoatNumber->' . Constant::$licenseStatus[$cancelRequest->status] . '</span> ';

                                if ($cancelRequest->status == 99) {
                                    $status = '<span class="badge badge-warning"><?=$myBoatNumber->To be Paid</span>';
                                }
                                if ($cancelRequest->status == 100) {
                                    $status = '<span class="badge badge-success"><?=$myBoatNumber->Waiting for final Approval</span>';

                                }
                                if ($cancelRequest->status == 101) {

                                    $status = '<span class="badge badge-info"><?=$myBoatNumber->' . Constant::$licenseStatus[$myBoatNumber->status] . '</span> ';

                                }


                                ?>
                                <tr>
                                    <!--                                    <th scope="row">1</th>-->
                                    <td colspan="2"><?= $cancelRequest->boatNumber->boat_number ?></td>
                                    <td><?= $status ?></td>
                                    <td></td>
                                </tr>
                            <?php }
                            ?>


                            </tbody>
                        </table>


                    </div>
                </div>
            </div>

        </div>


    </div>

  <div class="row">

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

        <div class="card mb-5 shadow-sm">

            <div class="card-body">

                <h3>
                    <?= Html::encode(
                        Yii::t(
                            'app',
                            'Boats Registration'
                        )
                    ) ?>
                </h3>

                <div class="table-responsive">

                    <table class="table table-striped">

                        <thead>
                        <tr>
                            <th scope="col">
                                <?= Html::encode(
                                    Yii::t('app', 'Number')
                                ) ?>
                            </th>

                            <th scope="col">
                                <?= Html::encode(
                                    Yii::t('app', 'Status')
                                ) ?>
                            </th>

                            <th scope="col">
                                <?= Html::encode(
                                    Yii::t('app', 'Note')
                                ) ?>
                            </th>

                            <th scope="col">
                                <?= Html::encode(
                                    Yii::t(
                                        'app',
                                        'Expire Date'
                                    )
                                ) ?>
                            </th>

                            <th scope="col">
                                <?= Html::encode(
                                    Yii::t('app', 'Action')
                                ) ?>
                            </th>
                        </tr>
                        </thead>

                        <tbody>

                        <?php
                        /*
                         * National and High Seas application buttons
                         * are available only when the original
                         * registration has status 101 or 403.
                         */
                        $allowedFirstRegistrationStatuses = [
                            101,
                            403,
                        ];
                        ?>

                        <?php foreach ($myBoats as $myBoat): ?>

                            <?php
                            $action = '';
                            $note = '';

                            $boatNumber = trim(
                                (string) (
                                    $myBoat
                                        ->boatNumber
                                        ?->boat_number
                                    ?? ''
                                )
                            );

                            $boatTypeCode = strtoupper(
                                trim(
                                    (string) (
                                        $myBoat
                                            ->boatNumber
                                            ?->boatType
                                            ?->code
                                        ?? ''
                                    )
                                )
                            );

                            /*
                             * Boat-registration licence records are
                             * indexed by fisherman_registerd_boat.id.
                             */
                            $viewBoatId = (int) (
                                $myBoat->id ?? 0
                            );

                            $boatLicenses =
                                $myBoatsLicenseMap[$viewBoatId]
                                ?? [];

                            /*
                             * Find the original registration.
                             *
                             * renew = 0 identifies the original
                             * registration.
                             *
                             * If duplicate original records exist,
                             * use the lowest nid.
                             */
                            $firstBoatRegistration = null;

                            foreach (
                                $boatLicenses
                                as $registrationRecord
                            ) {
                                if (
                                    (int) (
                                        $registrationRecord->renew
                                        ?? -1
                                    ) !== 0
                                ) {
                                    continue;
                                }

                                if (
                                    $firstBoatRegistration === null
                                    || (int) (
                                        $registrationRecord->nid
                                        ?? 0
                                    ) < (int) (
                                        $firstBoatRegistration->nid
                                        ?? 0
                                    )
                                ) {
                                    $firstBoatRegistration =
                                        $registrationRecord;
                                }
                            }

                            /*
                             * fisherman_registerd_boat_license.id is
                             * the related fisherman_registerd_boat.id.
                             *
                             * Use this value as the authoritative base
                             * registration ID for National and High Seas
                             * create actions.
                             */
                            $registeredBoatIdFromLicense =
                                (int) (
                                    $firstBoatRegistration->id
                                    ?? 0
                                );

                            /*
                             * Reload the actual base registration.
                             *
                             * SecurityHelper validates the model-bound
                             * ID during decryption, so the related
                             * FishermanRegisterdBoat row must exist.
                             */
                            $registeredBoat = null;

                            if ($registeredBoatIdFromLicense > 0) {
                                $registeredBoat =
                                    FishermanRegisterdBoat::find()
                                        ->where([
                                            'id' =>
                                                $registeredBoatIdFromLicense,
                                        ])
                                        ->one();
                            }

                            $registeredBoatId =
                                (int) (
                                    $registeredBoat->id
                                    ?? 0
                                );

                            /*
                             * Record unexpected ID mismatches.
                             */
                            if (
                                $registeredBoatId > 0
                                && $viewBoatId > 0
                                && $registeredBoatId
                                    !== $viewBoatId
                            ) {
                                Yii::warning(
                                    [
                                        'message' =>
                                            'Registered boat ID mismatch in fisherman profile.',
                                        'viewBoatId' =>
                                            $viewBoatId,
                                        'licenseBoatId' =>
                                            $registeredBoatId,
                                        'firstRegistrationNid' =>
                                            $firstBoatRegistration
                                                ->nid
                                            ?? null,
                                    ],
                                    'boat-license-buttons'
                                );
                            }

                            /*
                             * Both buttons require:
                             *
                             * 1. An original registration record
                             * 2. renew = 0
                             * 3. status = 101 or 403
                             * 4. A valid fisherman_registerd_boat row
                             */
                            $canApplyForLicenses =
                                $firstBoatRegistration !== null
                                && (int) (
                                    $firstBoatRegistration->renew
                                    ?? -1
                                ) === 0
                                && in_array(
                                    (int) (
                                        $firstBoatRegistration->status
                                        ?? 0
                                    ),
                                    $allowedFirstRegistrationStatuses,
                                    true
                                )
                                && $registeredBoat !== null
                                && $registeredBoatId > 0;

                            /*
                             * Original registration status.
                             */
                            if ($firstBoatRegistration !== null) {
                                $registrationStatusText =
                                    Constant::$licenseStatus[
                                        $firstBoatRegistration->status
                                    ]
                                    ?? Yii::t(
                                        'app',
                                        'Unknown'
                                    );

                                $registrationStatus =
                                    Html::tag(
                                        'span',
                                        Yii::t(
                                            'app',
                                            $registrationStatusText
                                        )
                                    );
                            } else {
                                $registrationStatus =
                                    Html::tag(
                                        'span',
                                        Yii::t(
                                            'app',
                                            'Registration not found'
                                        ),
                                        [
                                            'class' =>
                                                'badge badge-secondary',
                                        ]
                                    );
                            }

                            $hasNationalLicense =
                                !empty(
                                    $myBoat->nationalLicenses
                                );

                            $hasHighseasLicense =
                                !empty(
                                    $myBoat->highseasLicenses
                                );

                            /*
                             * National License.
                             *
                             * Preserve the existing token contract in
                             * NationalLicenseController::actionCreate().
                             *
                             * Token value:
                             * fisherman_registerd_boat.id
                             */
                            if ($hasNationalLicense) {
                                $note .= Html::tag(
                                    'span',
                                    Yii::t(
                                        'app',
                                        'Applied for National License'
                                    ),
                                    [
                                        'class' =>
                                            'badge badge-success',
                                    ]
                                );
                            } elseif ($canApplyForLicenses) {
                                $nationalBoatToken =
                                    SecurityHelper::encryptId(
                                        FishermanRegisterdBoatLicense::class,
                                        $registeredBoatId
                                    );

                                $action .= Html::a(
                                    Yii::t(
                                        'app',
                                        'Apply for National License'
                                    ),
                                    [
                                        '/national-license/create',
                                        'boat' =>
                                            $nationalBoatToken,
                                    ],
                                    [
                                        'class' =>
                                            'btn btn-primary btn-sm mb-1',
                                    ]
                                );
                            }

                            /*
                             * High Seas License.
                             *
                             * HighseasLicenseController::actionCreate()
                             * decrypts this token using:
                             *
                             * FishermanRegisterdBoat::class
                             *
                             * Therefore this token must contain the
                             * verified fisherman_registerd_boat.id.
                             */
                            if ($boatTypeCode === 'IMUL') {
                                if ($hasHighseasLicense) {
                                    if ($note !== '') {
                                        $note .= '<br>';
                                    }

                                    $note .= Html::tag(
                                        'span',
                                        Yii::t(
                                            'app',
                                            'Applied for Highseas License'
                                        ),
                                        [
                                            'class' =>
                                                'badge badge-success',
                                        ]
                                    );
                                } elseif (
                                    $canApplyForLicenses
                                ) {
                                    if ($action !== '') {
                                        $action .= ' ';
                                    }

                                    $highseasBoatToken =
                                        SecurityHelper::encryptId(
                                            FishermanRegisterdBoat::class,
                                            $registeredBoatId
                                        );

                                    $action .= Html::a(
                                        Yii::t(
                                            'app',
                                            'Apply for Highseas License'
                                        ),
                                        [
                                            '/highseas-license/create',
                                            'boat' =>
                                                $highseasBoatToken,
                                        ],
                                        [
                                            'class' =>
                                                'btn btn-success btn-sm mb-1',
                                        ]
                                    );
                                }
                            }

                            /*
                             * Display a warning when an applicable
                             * licence has not been created but the
                             * original registration does not meet the
                             * required conditions.
                             */
                            $nationalLicenseMissing =
                                !$hasNationalLicense;

                            $highseasLicenseMissing =
                                $boatTypeCode === 'IMUL'
                                && !$hasHighseasLicense;

                            if (
                                !$canApplyForLicenses
                                && (
                                    $nationalLicenseMissing
                                    || $highseasLicenseMissing
                                )
                            ) {
                                if ($note !== '') {
                                    $note .= '<br>';
                                }

                                if (
                                    $firstBoatRegistration === null
                                ) {
                                    $warningMessage =
                                        Yii::t(
                                            'app',
                                            'The original boat registration was not found.'
                                        );
                                } elseif (
                                    $registeredBoat === null
                                ) {
                                    $warningMessage =
                                        Yii::t(
                                            'app',
                                            'The related registered boat record was not found.'
                                        );
                                } else {
                                    $warningMessage =
                                        Yii::t(
                                            'app',
                                            'The first boat registration must complete before applying for a licence.'
                                        );
                                }

                                $note .= Html::tag(
                                    'span',
                                    $warningMessage,
                                    [
                                        'class' =>
                                            'badge badge-warning',
                                    ]
                                );
                            }

                            /*
                             * Find the latest licence by the highest nid.
                             */
                            $latestLicenseNid = null;

                            foreach (
                                $boatLicenses
                                as $registrationRecord
                            ) {
                                $currentNid =
                                    (int) (
                                        $registrationRecord->nid
                                        ?? 0
                                    );

                                if (
                                    $currentNid > 0
                                    && (
                                        $latestLicenseNid === null
                                        || $currentNid
                                            > $latestLicenseNid
                                    )
                                ) {
                                    $latestLicenseNid =
                                        $currentNid;
                                }
                            }

                            /*
                             * Check for an existing pending renewal.
                             */
                            $hasPendingRenewal =
                                $registeredBoatId > 0
                                && FishermanRegisterdBoatLicense::find()
                                    ->where([
                                        'id' =>
                                            $registeredBoatId,
                                        'renew' => 1,
                                        'status' =>
                                            Constant::Pending,
                                    ])
                                    ->exists();
                            ?>

                            <tr style="background-color: #b3d7ff;">

                                <td>
                                    <?= Html::encode(
                                        $boatNumber
                                    ) ?>
                                </td>

                                <td>
                                    <?= $registrationStatus ?>
                                </td>

                                <td>
                                    <?= $note ?>
                                </td>

                                <td>
                                    <?= Html::encode(
                                        Yii::t(
                                            'app',
                                            'N/A'
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <?= $action ?>
                                </td>

                            </tr>

                            <?php foreach (
                                $boatLicenses
                                as $mybt
                            ): ?>

                                <?php
                                $licenseAction = '';
                                $expireNotice = '';

                                $licenseNid =
                                    (int) (
                                        $mybt->nid
                                        ?? 0
                                    );

                                if ($licenseNid <= 0) {
                                    continue;
                                }

                                $licenseToken =
                                    SecurityHelper::encryptId(
                                        FishermanRegisterdBoatLicense::class,
                                        $licenseNid
                                    );

                                $statusText =
                                    Constant::$licenseStatus[
                                        $mybt->status
                                    ]
                                    ?? Yii::t(
                                        'app',
                                        'Unknown'
                                    );

                                $status = Html::tag(
                                    'span',
                                    Yii::t(
                                        'app',
                                        $statusText
                                    )
                                );

                                $isPending =
                                    (int) $mybt->status
                                    === (int) Constant::Pending;

                                $isFirstRegistration =
                                    (int) (
                                        $mybt->renew
                                        ?? 0
                                    ) === 0;

                                $isLatestLicense =
                                    $latestLicenseNid !== null
                                    && $licenseNid
                                        === $latestLicenseNid;

                                if (!$isPending) {
                                    $licenseViewRoute =
                                        $isFirstRegistration
                                            ? '/boat-registration/license-view-first'
                                            : '/boat-registration/license-view';

                                    $licenseAction .= Html::a(
                                        Yii::t(
                                            'app',
                                            'View License'
                                        ),
                                        [
                                            $licenseViewRoute,
                                            'token' =>
                                                $licenseToken,
                                        ],
                                        [
                                            'class' =>
                                                'btn btn-primary btn-sm mb-1',
                                        ]
                                    );

                                    $dateDiff = null;

                                    if (
                                        !empty(
                                            $mybt->expire_date
                                        )
                                    ) {
                                        $dateDiff =
                                            CommonService::getDateDiffWithCurrentDate(
                                                $mybt->expire_date
                                            );
                                    }

                                    if (
                                        $dateDiff !== null
                                        && (int) $dateDiff <= 30
                                    ) {
                                        $dateDiff =
                                            (int) $dateDiff;

                                        if ($dateDiff < 0) {
                                            $expireMessage =
                                                Yii::t(
                                                    'app',
                                                    'Expired {days} day(s) ago',
                                                    [
                                                        'days' =>
                                                            abs(
                                                                $dateDiff
                                                            ),
                                                    ]
                                                );
                                        } elseif (
                                            $dateDiff === 0
                                        ) {
                                            $expireMessage =
                                                Yii::t(
                                                    'app',
                                                    'Expiring today'
                                                );
                                        } else {
                                            $expireMessage =
                                                Yii::t(
                                                    'app',
                                                    'Expiring in {days} day(s)',
                                                    [
                                                        'days' =>
                                                            $dateDiff,
                                                    ]
                                                );
                                        }

                                        $expireNotice =
                                            Html::tag(
                                                'span',
                                                $expireMessage,
                                                [
                                                    'class' =>
                                                        'badge badge-warning ml-1',
                                                ]
                                            );

                                        if (
                                            !$hasPendingRenewal
                                            && $isLatestLicense
                                        ) {
                                            $licenseAction .= ' ';

                                            $licenseAction .=
                                                Html::a(
                                                    Yii::t(
                                                        'app',
                                                        'Renew Now'
                                                    ),
                                                    [
                                                        '/boat-registration/renew',
                                                        'token' =>
                                                            $licenseToken,
                                                    ],
                                                    [
                                                        'class' =>
                                                            'btn btn-warning btn-sm mb-1 boat-license-renew',
                                                    ]
                                                );
                                        }
                                    }
                                } else {
                                    $licenseAction =
                                        Html::tag(
                                            'span',
                                            Yii::t(
                                                'app',
                                                'In progress'
                                            ),
                                            [
                                                'class' =>
                                                    'badge badge-info',
                                            ]
                                        );
                                }

                                $expiryDate =
                                    !empty(
                                        $mybt->expire_date
                                    )
                                        ? (string) $mybt->expire_date
                                        : Yii::t(
                                            'app',
                                            'N/A'
                                        );
                                ?>

                                <tr style="background-color: lightyellow;">

                                    <td>
                                        <?= Html::encode(
                                            $boatNumber
                                            . ' - '
                                            . Yii::t(
                                                'app',
                                                'License'
                                            )
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= $status ?>
                                    </td>

                                    <td>
                                        <?php if (
                                            $isFirstRegistration
                                        ): ?>

                                            <?= Html::tag(
                                                'span',
                                                Yii::t(
                                                    'app',
                                                    'First Registration'
                                                ),
                                                [
                                                    'class' =>
                                                        'badge badge-info',
                                                ]
                                            ) ?>

                                        <?php else: ?>

                                            <?= Html::tag(
                                                'span',
                                                Yii::t(
                                                    'app',
                                                    'Renewal'
                                                ),
                                                [
                                                    'class' =>
                                                        'badge badge-secondary',
                                                ]
                                            ) ?>

                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?= Html::encode(
                                            $expiryDate
                                        ) ?>

                                        <?= $expireNotice ?>
                                    </td>

                                    <td>
                                        <?= $licenseAction ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endforeach; ?>

                        <?php if (empty($myBoats)): ?>

                            <tr>
                                <td
                                    colspan="5"
                                    class="text-center text-muted"
                                >
                                    <?= Html::encode(
                                        Yii::t(
                                            'app',
                                            'No boat registrations were found.'
                                        )
                                    ) ?>
                                </td>
                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>
    <div class="modal fade" id="paynow-modal" tabindex="-1" role="dialog" aria-labelledby="paynow" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= Yii::t('app', 'Upload Payment Receipt') ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="hidden" id="boat-id"/>
                            <div class="form-group">
                                <label><?= Yii::t('app', 'Upload Payment Receipt') ?></label>
                                <input type="file" class="form-control" id="customFile"/>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label><?= Yii::t('app', 'Enter Paid Amount') ?></label>
                                <input type="text" class="form-control" id="amount"/>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label><?= Yii::t('app', 'Ref number') ?></label>
                                <input type="text" class="form-control" id="ref"/>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary " data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary pay-boat-number-submit">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="paynow-yard-modal" tabindex="-1" role="dialog" aria-labelledby="paynow"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Payment Receipt for yard license</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="hidden" id="yard-id"/>
                            <div class="form-group">
                                <label>Upload Payment Receipt</label>
                                <input type="file" class="form-control" id="customFile"/>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Enter Paid Amount</label>
                                <input type="text" class="form-control" id="amount"/>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Ref number</label>
                                <input type="text" class="form-control" id="ref-yard"/>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary " data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary pay-yard-submit">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="paynow-boat-reg-modal" tabindex="-1" role="dialog" aria-labelledby="paynow"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Payment Receipt</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="hidden" id="boat-id-reg"/>
                            <div class="form-group">
                                <label>Upload Payment Receipt</label>
                                <input type="file" class="form-control" id="customFile"/>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Enter Paid Amount</label>
                                <input type="text" class="form-control" id="amount-reg"/>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Ref number</label>
                                <input type="text" class="form-control" id="ref-reg"/>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary " data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary pay-boat-register-submit">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="paynow-skipper-licence-modal" tabindex="-1" role="dialog" aria-labelledby="paynow"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Skipper licence Payment Receipt</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="hidden" id="skipper_id"/>
                            <div class="form-group">
                                <label>Upload Payment Receipt</label>
                                <input type="file" class="form-control" id="customFile"/>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Enter Paid Amount</label>
                                <input type="text" class="form-control" id="amount-skipper"/>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Ref number</label>
                                <input type="text" class="form-control" id="ref-skipper"/>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary " data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary pay-skipper-submit">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="paynow-national-licence-modal" tabindex="-1" role="dialog" aria-labelledby="paynow"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload National licence Payment Receipt</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="hidden" id="national_id"/>
                            <div class="form-group">
                                <label>Upload Payment Receipt</label>
                                <input type="file" class="form-control" id="customFile"/>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Enter Paid Amount</label>
                                <input type="text" class="form-control" id="amount-national"/>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Ref number</label>
                                <input type="text" class="form-control" id="ref-national"/>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary " data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary pay-national-submit">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="paynow-highseas-licence-modal" tabindex="-1" role="dialog" aria-labelledby="paynow"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Highseas licence Payment Receipt</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="hidden" id="highseas_id"/>
                            <div class="form-group">
                                <label>Upload Payment Receipt</label>
                                <input type="file" class="form-control" id="customFile"/>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Enter Paid Amount</label>
                                <input type="text" class="form-control" id="amount-highseas"/>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Ref number</label>
                                <input type="text" class="form-control" id="ref-highseas"/>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary " data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary pay-highseas-submit">Submit</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="licence-view-modal" tabindex="-1" role="dialog" aria-labelledby="paynow"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">License view</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">

                        </div>

                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary " data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary pay-skipper-submit">Submit</button>
                </div>
            </div>
        </div>
    </div>

    

    <!--    --><?php //Pjax::end(); ?>
<?php } ?>
