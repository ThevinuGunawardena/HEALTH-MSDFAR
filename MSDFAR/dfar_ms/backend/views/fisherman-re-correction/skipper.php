<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ProfileFisherman $model */
/** @var backend\models\Skipper $skipper */
/** @var backend\models\SkipperReCorrection $registration */
/** @var int $mainId */
/** @var bool $uidValid */
/** @var bool $nicValid */

$this->title = 'Fix Skipper Details';
?>
<div class="skipper-re-correction-index">



    <?php if (!$uidValid): ?>
        <div class="alert alert-warning">
            The Skipper UID on file (<strong><?= Html::encode($skipper->skipper_uid) ?></strong>) is not in a valid format.
        </div>
    <?php endif; ?>

    <?php if (!$nicValid): ?>
        <div class="alert alert-warning">
            The NIC number on file (<strong><?= Html::encode($model->nic) ?></strong>) is not in a valid format.
        </div>
    <?php endif; ?>

    <div class="alert alert-info">
        Please correct the field(s) above below, or click Ignore to continue anyway.
    </div>

    <?php $form = ActiveForm::begin(); ?>

        <?php if (!$uidValid): ?>
            <?= $form->field($registration, 'skipper_uid')->textInput(['maxlength' => true, 'autofocus' => true]) ?>
        <?php endif; ?>

        <?php if (!$nicValid): ?>
            <?= $form->field($registration, 'nic')->textInput([
                'maxlength' => true,
                'autofocus' => $uidValid, // only autofocus this if the UID field isn't shown
            ]) ?>
        <?php endif; ?>

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