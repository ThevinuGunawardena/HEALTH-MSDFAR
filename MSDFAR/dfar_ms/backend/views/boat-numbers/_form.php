<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\ProfileFisherman;
use backend\services\CommonService;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\BoatNumbers $model */
/** @var string|null $token */
/** @var array $yardList */
/** @var array $districtList */
/** @var array $boatTypeArray */

$token = trim((string) ($token ?? ''));

$ownerData = [];

if (!empty($model->owner)) {
    $ownerData = ArrayHelper::map(
        ProfileFisherman::find()
            ->where([
                'id' => (int) $model->owner,
            ])
            ->all(),
        'id',
        static function (ProfileFisherman $owner): string {
            return 'ID-'
                . (string) $owner->fisherman_uid
                . ', NIC:'
                . (string) $owner->nic;
        }
    );
}

$formAction = $model->getIsNewRecord()
    ? ['/boat-numbers/create']
    : [
        '/boat-numbers/update',
        'token' => $token,
    ];

$cancelRoute = $model->getIsNewRecord()
    ? ['/boat-numbers/index']
    : [
        '/boat-numbers/view',
        'token' => $token,
    ];

$selectedBoatDesign = Json::htmlEncode(
    (string) ($model->boat_design ?? '')
);

$boatDesignScript = <<<JS
(function () {
    'use strict';

    const yardDropdown = $('#boatnumbers-yard');
    const designDropdown = $('#boatnumbers-boat_design');
    const selectedBoatDesign = {$selectedBoatDesign};

    if (!yardDropdown.length || !designDropdown.length) {
        return;
    }

    function resetDesigns() {
        designDropdown
            .empty()
            .append(
                $('<option>', {
                    value: '',
                    text: 'Select'
                })
            );
    }

    function loadDesigns(yardId, selectedDesign) {
        if (!yardId) {
            resetDesigns();
            return;
        }

        if (typeof window.loadBoatDesignByYard !== 'function') {
            console.error(
                'loadBoatDesignByYard() is not available.'
            );
            return;
        }

        window.loadBoatDesignByYard(
            yardId,
            selectedDesign || ''
        );
    }

    if (yardDropdown.val()) {
        loadDesigns(
            yardDropdown.val(),
            selectedBoatDesign
        );
    } else {
        resetDesigns();
    }

    yardDropdown
        .off('change.boatNumbers')
        .on('change.boatNumbers', function () {
            loadDesigns($(this).val(), '');
        });
})();
JS;

$this->registerJs(
    $boatDesignScript,
    \yii\web\View::POS_READY
);
?>

<div class="boat-numbers-form">
    <?php $form = ActiveForm::begin([
        'action' => $formAction,
        'method' => 'post',
        'options' => [
            'class' => 'userform',
        ],
    ]); ?>

    <div class="row">
        <div class="col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <?php if (!UserTypeUtil::hasType(Constant::MANAGEMENT)): ?>
                        <div class="row">
                            <div class="col-xl-12">
                                <?= $form->field(
                                    $model,
                                    'owner'
                                )->widget(
                                    Select2::class,
                                    [
                                        'data' => $ownerData,
                                        'options' => [
                                            'placeholder' => Yii::t(
                                                'app',
                                                'Search for a boat owner.'
                                            ),
                                        ],
                                        'pluginOptions' => [
                                            'allowClear' => true,
                                            'minimumInputLength' => 3,
                                            'language' => [
                                                'errorLoading' =>
                                                    new JsExpression(
                                                        "function () {
                                                            return 'Waiting for results...';
                                                        }"
                                                    ),
                                            ],
                                            'ajax' => [
                                                'url' => Url::to([
                                                    '/fisherman/search-global',
                                                ]),
                                                'dataType' => 'json',
                                                'delay' => 250,
                                                'data' =>
                                                    new JsExpression(
                                                        'function (params) {
                                                            return {
                                                                q: params.term
                                                            };
                                                        }'
                                                    ),
                                            ],
                                        ],
                                    ]
                                ) ?>
                            </div>

                            <div class="col-xl-6">
                                <?= $form->field(
                                    $model,
                                    'yard'
                                )->dropDownList(
                                    $yardList,
                                    [
                                        'prompt' => Yii::t(
                                            'app',
                                            'Select'
                                        ),
                                    ]
                                ) ?>
                            </div>

                            <div class="col-xl-6">
                                <?= $form->field(
                                    $model,
                                    'boat_design'
                                )->dropDownList(
                                    [],
                                    [
                                        'prompt' => Yii::t(
                                            'app',
                                            'Select'
                                        ),
                                    ]
                                ) ?>
                            </div>

                            <div class="col-xl-6">
                                <?= $form->field(
                                    $model,
                                    'boat_type'
                                )->dropDownList(
                                    !empty($boatTypeArray)
                                        ? $boatTypeArray
                                        : CommonService::
                                            getBoatCategoriesArray(),
                                    [
                                        'prompt' => Yii::t(
                                            'app',
                                            'Select'
                                        ),
                                    ]
                                ) ?>
                            </div>

                            <div class="col-xl-6">
                                <?= $form->field(
                                    $model,
                                    'hull_number'
                                )->textInput([
                                    'maxlength' => true,
                                ]) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (UserTypeUtil::hasType(Constant::MANAGEMENT)): ?>
                        <div class="row">
                            <div class="col-xl-12">
                                <?= $form->field(
                                    $model,
                                    'additional_conditions'
                                )->textarea([
                                    'rows' => 5,
                                ]) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (!UserTypeUtil::hasType(Constant::MANAGEMENT)): ?>
            <div class="col-12">
                <div class="card mb-5 shadow-sm">
                    <div class="card-body">
                        <h4>
                            <?= Html::encode(
                                Yii::t(
                                    'app',
                                    'Boat Propelling Details'
                                )
                            ) ?>
                        </h4>

                        <div class="row">
                            <div class="col-lg-6">
                                <?= Html::label(
                                    Yii::t(
                                        'app',
                                        'Fisheries Management Area'
                                    ),
                                    'fi-district'
                                ) ?>

                                <?= Html::textInput(
                                    'fi_district',
                                    (string) (
                                        $model->fisheriesDistrict?->name
                                        ?? ''
                                    ),
                                    [
                                        'id' => 'fi-district',
                                        'class' => 'form-control',
                                        'readonly' => true,
                                    ]
                                ) ?>
                            </div>

                            <?php if (UserTypeUtil::hasType(Constant::ADMIN)): ?>
                                <div class="col-lg-12">
                                    <?= $form->field(
                                        $model,
                                        'boat_number'
                                    )->textInput([
                                        'maxlength' => true,
                                    ]) ?>
                                </div>
                            <?php endif; ?>

                            <div class="col-lg-12">
                                <?= $form->field(
                                    $model,
                                    'remarks'
                                )->textarea([
                                    'maxlength' => true,
                                    'rows' => 4,
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body d-flex justify-content-end">
                    <?= Html::a(
                        Yii::t('app', 'Cancel'),
                        $cancelRoute,
                        [
                            'class' => 'btn btn-warning mr-2',
                        ]
                    ) ?>

                    <?= Html::submitButton(
                        Yii::t('app', 'Submit'),
                        [
                            'class' => 'btn btn-primary',
                            'name' => 'btnsubmit',
                        ]
                    ) ?>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
