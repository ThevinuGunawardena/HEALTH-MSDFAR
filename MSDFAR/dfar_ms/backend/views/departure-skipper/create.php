<?php

/** @var yii\web\View $this */
/** @var backend\models\DepartureSkipper|app\models\DepartureSkipperC $model */

$this->title = Yii::t(
    'app',
    'Create Departure Skipper/Crew Member'
);
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Departure Skippers'),
    'url' => ['index'],
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <?= $this->render('_form', [
                'model' => $model,
                'token' => null,
                'formAction' => null,
            ]) ?>

        </div>
    </div>
</div>
