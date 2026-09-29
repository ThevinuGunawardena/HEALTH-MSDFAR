<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\BoatNumberOwnersLog $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="boat-number-owners-log-form">

    <?php $form = ActiveForm::begin(['options' => [
        'class' => 'userform'
    ]]); ?>
    <!--    --><?php //= $form->field($model, 'boat_number')->textInput(['maxlength' => true]) ?>
    <div class="row">
        <div class="col-lg-12">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        </div>
        <div class="col-lg-12">
            <?= $form->field($model, 'nic')->textInput(['maxlength' => true]) ?>

        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'from_date')->textInput(['type' => "date", "max" => date("Y-m-d"), 'onkeydown' => "return false"]) ?>

        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'to_date')->textInput(['type' => "date", "max" => date("Y-m-d"), 'onkeydown' => "return false"]) ?>

        </div>

        <?php if ($boatRegAvailable == true) { ?>
            <div class="col-lg-6">
                <hr>
                <div class="form-group field-boatnumberownerslog-first_reg_date">
                    <label class="control-label" for="boatnumberownerslog-first_reg_date">Boat First Registration
                        date</label>
                    <input type="date" id="boatnumberownerslog-nic" class="form-control"
                           name="BoatNumberOwnersLog[first_reg_date]" value="<?= $boatRegDate ?>"
                           max=<?= date("Y-m-d") ?>>
                </div>
            </div>
        <?php } ?>
        <div class="col-lg-12">
            <hr>
            <div class="form-group field-boatnumberownerslog-boat_length">
                <label class="control-label" for="boatnumberownerslog-boat_length">Boat length <strong>(Length available
                        in boat design: <?= $boatDesignLength == "" ? "NA" : $boatDesignLength ?>)</strong>
                    <i>* If boat length is different than boat design length please add it here. otherwise keep it
                        empty</i>
                </label>
                <input type="number" step=".01" id="boatnumberownerslog-boat_length" class="form-control"
                       name="BoatNumberOwnersLog[boat_length]" value="<?= $boatNumberLength ?>"
                       min=0>
            </div>
        </div>

    </div>


    <!--    --><?php //= $form->field($model, 'status')->textInput() ?>

    <!--    --><?php //= $form->field($model, 'added_by')->textInput() ?>

    <!--    --><?php //= $form->field($model, 'created_time')->textInput() ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
