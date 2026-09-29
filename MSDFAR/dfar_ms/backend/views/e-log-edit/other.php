<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'E-Log Entry';
// $this->params['breadcrumbs'][] = $this->title;
?>

<div class="container mt-4">

    <div class="card">
        <div class="card-body text-center">

            <?= Html::a(
                'Gear Data',
                Url::to(['e-log-edit/geardata', 'id' => $model->id]),
                ['class' => 'btn btn-primary m-2']
            ) ?>
            <?= Html::a(
                'Set Data',
                Url::to(['e-log-edit/setdata', 'id' => $model->id]),
                ['class' => 'btn btn-success m-2']
            ) ?>

            <?= Html::a(
                'Finish',
                Url::to(['e-log-edit/index']),
                [
                    'class' => 'btn btn-danger m-2',
                    'data' => [
                        'confirm' => 'Are you sure you want to finish?',
                    ],
                ]
            ) ?>
        </div>
    </div>

</div>