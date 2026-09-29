<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\DepartureRequests $model */

$this->title = Yii::t('app', 'Create Departure Requests');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Departure Requests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="container mt-5">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

        <div class="card shadow-sm mb-5">
            <div class="card-body">
                <h1><?= Html::encode($this->title) ?></h1>

                <?= $this->render('_form', [
                        'model' => $model,
                        'readOnly' => false,
                        'update' => false
                ]) ?>

            </div>
        </div>
    </div>
</div>