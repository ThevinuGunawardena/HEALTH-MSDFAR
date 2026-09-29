<?php

use backend\config\Constant;
use backend\models\ProfileFisherman;
use backend\models\SkipperRenewSearch;
use backend\services\CommonService;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var SkipperRenewSearch $model */
/** @var ActiveForm $form */

$ownerData = [];

if (!empty($model->fisherman_id)) {
    $ownerData = ArrayHelper::map(
        ProfileFisherman::find()
            ->where([
                'id' => $model->fisherman_id,
            ])
            ->all(),
        'id',
        static function (
            ProfileFisherman $profile
        ): string {
            return 'ID-'
                . (string) $profile->fisherman_uid
                . ', NIC: '
                . (string) $profile->nic;
        }
    );
}
?>

<div class="skipper-renew-search">

    <?php $form = ActiveForm::begin([
        'action' => ['/skipper-renew/index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1,
        ],
    ]); ?>

    <div class="row">

        <div class="col-xl-6">
            <?= $form->field(
                $model,
                'fisherman_id'
            )->widget(
                Select2::class,
                [
                    'data' => $ownerData,
                    'options' => [
                        'placeholder' =>
                            Yii::t(
                                'app',
                                'Search for a fisherman'
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
                            'data' => new JsExpression(
                                'function(params) {
                                    return {
                                        q: params.term
                                    };
                                }'
                            ),
                        ],
                        'escapeMarkup' =>
                            new JsExpression(
                                'function(markup) {
                                    return markup;
                                }'
                            ),
                        'templateResult' =>
                            new JsExpression(
                                'function(item) {
                                    return item.text;
                                }'
                            ),
                        'templateSelection' =>
                            new JsExpression(
                                'function(item) {
                                    return item.text;
                                }'
                            ),
                    ],
                ]
            ) ?>
        </div>

        <div class="col-xl-6">
            <?= $form->field(
                $model,
                'status'
            )->dropDownList(
                Constant::$licenseStatus,
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
                'fisheries_district'
            )->dropDownList(
                CommonService::getFIDistrictArray(),
                [
                    'prompt' => Yii::t(
                        'app',
                        'Select'
                    ),
                ]
            ) ?>
        </div>

    </div>

    <div class="form-group">
        <?= Html::submitButton(
            Yii::t('app', 'Search'),
            [
                'class' => 'btn btn-primary',
            ]
        ) ?>

        <?= Html::a(
            Yii::t('app', 'Reset'),
            ['/skipper-renew/index'],
            [
                'class' => 'btn btn-outline-secondary',
            ]
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
