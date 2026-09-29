<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\ProfileFisherman;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\BoatNumbersSearch $model */

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
?>

<div class="boat-numbers-search">
    <?php $form = ActiveForm::begin([
        'action' => ['/boat-numbers/index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field(
                $model,
                'boat_number'
            )->textInput([
                'maxlength' => true,
            ]) ?>
        </div>

        <div class="col-lg-6">
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
                            'errorLoading' => new JsExpression(
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
                            'data' => new JsExpression(
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

        <?php if (UserTypeUtil::hasType(Constant::DG)): ?>
            <div class="col-lg-6">
                <?= $form->field(
                    $model,
                    'approval_stage'
                )->dropDownList(
                    Constant::getUserTypesByCategory('main'),
                    [
                        'prompt' => Yii::t('app', 'Select'),
                    ]
                ) ?>
            </div>
        <?php endif; ?>

        <div class="col-lg-6">
            <?= $form->field(
                $model,
                'status'
            )->dropDownList(
                Constant::$licenseStatus,
                [
                    'prompt' => Yii::t('app', 'Select'),
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
            ['/boat-numbers/index'],
            [
                'class' => 'btn btn-outline-secondary',
            ]
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
