<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ProfileFisherman $model */
/** @var backend\models\FishermanReCorrection $registration */
/** @var int $fishermanId */
/** @var int $mainId */

$this->title = 'Fix Fisherman NIC';
?>
<div class="fisherman-re-correction-index">

    

    <div class="alert alert-warning">
        The NIC number on file (<strong><?= Html::encode($model->nic) ?></strong>) is not in a valid format.
        Please correct it below, or click Ignore to continue anyway.
    </div>

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($registration, 'nic')->textInput(['maxlength' => true, 'autofocus' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>

            <?= Html::a('Ignore', [
                'ignore',
                'fishermanId' => $model->id,
                'mainId' => $mainId ?? null,
            ], ['class' => 'btn btn-default']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>