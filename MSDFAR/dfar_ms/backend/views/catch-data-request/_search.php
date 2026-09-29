<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\models\BoatNumbers;
use kartik\select2\Select2;

/** @var yii\web\View $this */
/** @var backend\models\CatchDataRequestSearch $model */
/** @var yii\widgets\ActiveForm $form */


$boatData = ArrayHelper::map(
    BoatNumbers::find()
        ->select(['boat_numbers.id', 'boat_numbers.boat_number'])
        ->innerJoinWith('fishermanRegisterdBoatLicenses')
        ->innerJoin(
            'catch_data_request',
            'catch_data_request.boat_registration_id = boat_numbers.id'
        )
        ->where([
            'fisherman_registerd_boat_license.status' => 101
        ])
        ->groupBy(['boat_numbers.id']) // 🔥 avoid duplicates
        ->asArray()
        ->all(),
    'id',
    'boat_number'
);
?>

<div class="catch-data-request-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

     <?php //   $form->field($model, 'id') ?>

      <?php //  $form->field($model, 'landing_date') ?>

<?= $form->field($model, 'boat_registration_id')->widget(Select2::classname(), [
    'data' => $boatData,
    'options' => [
        'placeholder' => 'Select boat number...',
    ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
     <?php //  $form->field($model, 'unloading_harbour') ?>

     <?php //  $form->field($model, 'fishing_gear_type') ?>

    <?php // echo $form->field($model, 'log_book_no') ?>

    <?php // echo $form->field($model, 'log_book_page_no') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
<?= Html::a(Yii::t('app', 'Reset'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>    </div>

    <?php ActiveForm::end(); ?>

</div>
