<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\ProfileFisherman;
use backend\services\CommonService;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\BoatNumbers $model */
/** @var array $yardList */
/** @var array $boatTypeArray */

$this->title = Yii::t('app', 'Add Manual Boat Numbers');

$ownerData = [];

if (!empty($model->owner)) {
    $ownerModel = ProfileFisherman::findOne($model->owner);

    if ($ownerModel !== null) {
        $ownerData = [
            $ownerModel->id =>
                'ID-' . (string) $ownerModel->fisherman_uid
                . ', NIC:' . (string) $ownerModel->nic,
        ];
    }
}

$searchOwnerUrl = Url::to(['/fisherman/search-global']);

$selectedBoatDesign = Json::htmlEncode(
    (string) ($model->boat_design ?? '')
);

$boatDesignScript = <<<JS
(function () {
    'use strict';

    const yardDropdown = $('#boatnumbers-yard');
    const designDropdown = $('#boatnumbers-boat_design');
    const selectedBoatDesign = {$selectedBoatDesign};

    if (!yardDropdown.length) {
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
    }

    yardDropdown
        .off('change.manualBoatNumber')
        .on(
            'change.manualBoatNumber',
            function () {
                loadDesigns($(this).val(), '');
            }
        );
})();
JS;

$this->registerJs(
    $boatDesignScript,
    \yii\web\View::POS_READY
);
?>

<div class="boat-numbers-form">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin([
        'action' => ['/boat-numbers/add-boat-number-manual'],
        'method' => 'post',
        'options' => [
            'class' => 'userform',
        ],
    ]); ?>

    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <?php if (!UserTypeUtil::hasType(Constant::MANAGEMENT)): ?>
                        <div class="row">
                            <?php if (UserTypeUtil::hasType(Constant::ADMIN)): ?>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                                    <?= $form->field(
                                        $model,
                                        'boat_number'
                                    )->textInput([
                                        'maxlength' => true,
                                    ]) ?>
                                </div>
                            <?php endif; ?>

                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                                <?= $form->field(
                                    $model,
                                    'owner'
                                )->widget(
                                    Select2::class,
                                    [
                                        'data' => $ownerData,
                                        'options' => [
                                            'placeholder' =>
                                                'Search for a boat owner ...',
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
                                                'url' => $searchOwnerUrl,
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

                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                                <?= $form->field(
                                    $model,
                                    'yard'
                                )->dropDownList(
                                    $yardList,
                                    [
                                        'prompt' => 'Select',
                                    ]
                                ) ?>
                            </div>

                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                                <?= $form->field(
                                    $model,
                                    'boat_design'
                                )->dropDownList(
                                    [],
                                    [
                                        'prompt' => 'Select',
                                    ]
                                ) ?>
                            </div>

                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                                <?= $form->field(
                                    $model,
                                    'boat_type'
                                )->dropDownList(
                                    !empty($boatTypeArray)
                                        ? $boatTypeArray
                                        : CommonService::
                                            getBoatCategoriesArray(),
                                    [
                                        'prompt' => 'Select',
                                    ]
                                ) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-end align-items-center gap-2">
                        <?= Html::a(
                            'Cancel',
                            ['/boat-numbers/index'],
                            [
                                'class' => 'btn btn-warning',
                            ]
                        ) ?>

                        <?= Html::submitButton(
                            'Submit',
                            [
                                'class' => 'btn btn-primary',
                                'name' => 'btnsubmit',
                            ]
                        ) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>