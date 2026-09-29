<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\ProfileFisherman;
use backend\services\CommonService;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\SkipperSearch $model */
/** @var yii\widgets\ActiveForm $form */

$ownerData = ArrayHelper::map(
    ProfileFisherman::find()
        ->where(['id' => $model->fisherman_id])
        ->all(),
    'id',
    static function (ProfileFisherman $profile): string {
        return 'ID-' . (string) $profile->fisherman_uid
            . ', NIC: ' . (string) $profile->nic;
    }
);
?>

<div class="skipper-search">

    <?php $form = ActiveForm::begin([
        'action' => ['/skipper/index'],
        'method' => 'get',
        'options' => ['data-pjax' => 1],
    ]); ?>

    <div class="row">
        <div class="col-xl-6">
            <?= $form->field($model, 'fisherman_id')->widget(
                Select2::class,
                [
                    'data' => $ownerData,
                    'options' => [
                        'placeholder' => Yii::t(
                            'app',
                            'Search for a fisherman...'
                        ),
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression(
                                "function () { return 'Waiting for results...'; }"
                            ),
                        ],
                        'ajax' => [
                            'url' => Url::to(['/fisherman/search-global']),
                            'dataType' => 'json',
                            'data' => new JsExpression(
                                'function(params) { return {q: params.term}; }'
                            ),
                        ],
                    ],
                ]
            ) ?>
        </div>

        <div class="col-lg-6">
            <?= UserTypeUtil::hasType(Constant::DG)
                ? $form->field($model, 'approval_stage')->dropDownList(
                    Constant::getUserTypesByCategory('main'),
                    ['prompt' => '-']
                )
                : '' ?>
        </div>

        <div class="col-xl-6">
            <?= $form->field($model, 'status')->dropDownList(
                Constant::$licenseStatus,
                ['prompt' => '-']
            ) ?>
        </div>

        <div class="col-xl-6">
            <?= $form->field($model, 'fisheries_district')->dropDownList(
                CommonService::getFIDistrictArray(),
                ['prompt' => '-']
            ) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(
            Yii::t('app', 'Search'),
            ['class' => 'btn btn-primary']
        ) ?>

        <?= Html::a(
            Yii::t('app', 'Reset'),
            ['/skipper/index'],
            ['class' => 'btn btn-outline-secondary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
