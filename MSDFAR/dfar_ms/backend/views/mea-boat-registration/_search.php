<?php

use backend\models\BoatNumbers;
use backend\models\ProfileFisherman;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\MeaBoatRegistrationSearch $model */
/** @var yii\widgets\ActiveForm $form */

$ownerData = ArrayHelper::map(ProfileFisherman::find()->andWhere(['id' => $model->owner])->all(), 'id', function ($model) {
    return "ID-" . $model['fisherman_uid'] . ", NIC:" . $model['nic'];
});
$boatData = ArrayHelper::map(BoatNumbers::find()->andWhere(['id' => $model->id])->all(), 'id', function ($model) {
    return $model['boat_number'];
});
?>

<div class="mea-boat-registration-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>






    <?= $form->field($model, 'owner')->widget(Select2::classname(), [
        'data' => $ownerData,

        'options' => ['placeholder' => 'Search for a fisherman ...'],
        'pluginOptions' => [
            'allowClear' => false,

            'minimumInputLength' => 3,
            'language' => [
                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
            ],
            'ajax' => [
                'url' => "../fisherman/search-global",
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {q:params.term}; }')
            ],
            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            'templateResult' => new JsExpression('function(boat_number) { return boat_number.text; }'),
            'templateSelection' => new JsExpression('function (boat_number) { return boat_number.text; }'),
        ],
    ]); ?>
    <?= $form->field($model, 'id')->widget(Select2::classname(), [
        'data' => $boatData,

        'options' => ['placeholder' => 'Search...'],
        'pluginOptions' => [
            'allowClear' => false,

            'minimumInputLength' => 3,
            'language' => [
                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
            ],
            'ajax' => [
                'url' => "../boat-numbers/search",
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {q:params.term}; }')
            ],
            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            'templateResult' => new JsExpression('function(boat_number) { return boat_number.text; }'),
            'templateSelection' => new JsExpression('function (boat_number) { return boat_number.text; }'),
        ],
    ]); ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
