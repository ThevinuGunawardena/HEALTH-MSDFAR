<?php

/** @var yii\web\View $this */
/** @var backend\models\ProfileFisherman $model */

use yii\widgets\ActiveForm;

$webURL = Yii::getAlias('@web');

$this->title = Yii::t('app', 'Update Fisherman: {name}', [
'name' => $model->fisherman_uid . ' ' . $model->first_name . ' ' . $model->last_name . '| Address: ' . $model->permanent_address,
]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Fishermen'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<style>
    @font-face {
        font-family: "dlsarala";
        src: url("https://msdfar.com/DLSarala.ttf") format("truetype");
        font-weight: normal;
        font-style: normal;
    }

    @font-face {
        font-family: "bamini";
        src: url("https://msdfar.com/Bamini.ttf") format("truetype");
        font-weight: normal;
        font-style: normal;
    }

    .sinhala-font {
        font-family: "dlsarala" !important;
        font-size: 18px !important;
        line-height: 1.6;
    }

    .tamil-font {
        font-family: "bamini" !important;
        font-size: 22px !important;
        line-height: 1.6;
    }

    input.sinhala-font,
    textarea.sinhala-font {
        font-family: "dlsarala" !important;
    }

    input.tamil-font,
    textarea.tamil-font {
        font-family: "bamini" !important;
    }
</style>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

            <div class="row fisherman_profile">
                <div class="col-xl-8 col-lg-12 col-md-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card mb-5 shadow-sm">
                                <div class="card-body">

                                    <h6 class="card-subtitle mb-2 text-muted">
                                        Please update name and address in Sinhala and Tamil language
                                    </h6>

                                    <a href="https://msdfar.com/DLSarala.ttf" target="_blank">
                                        Download (DLSarala) Sinhala Font
                                    </a>
                                    |
                                    <a href="https://msdfar.com/Bamini.ttf" target="_blank">
                                        Download (Bamini) Tamil Font
                                    </a>

                                    <hr>

                                    <div class="row">
                                        <div class="col-xl-12">
                                            <?= $form->field($model, 'name_sinhala')->textInput([
                                                'class' => 'form-control sinhala-font',
                                                'style' => 'font-family:dlsarala !important;'
                                            ]) ?>
                                        </div>

                                        <div class="col-xl-12">
                                            <?= $form->field($model, 'address_sinhala')->textarea([
                                                'class' => 'form-control sinhala-font',
                                                'style' => 'font-family:dlsarala !important;',
                                                'rows' => 4
                                            ]) ?>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xl-12">
                                            <?= $form->field($model, 'name_tamil')->textInput([
                                                'class' => 'form-control tamil-font',
                                                'style' => 'font-family:Nirmala UI !important; font-size:22px !important;'
                                            ]) ?>
                                        </div>

                                        <div class="col-xl-12">
                                           <?= $form->field($model, 'address_tamil')->textarea([
                                                'class' => 'form-control',
                                                'style' => 'font-family:Nirmala UI;',
                                                'rows' => 4
                                            ]) ?>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xl-8">
                                            <div class="row">
                                                <div class="col-xl-3">
                                                    <div class="form-group">
                                                        <button type="submit" class="btn btn-success">
                                                            Update
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>