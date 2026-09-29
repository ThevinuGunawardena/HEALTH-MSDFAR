<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Applicationtransportbechedemer $model */

$this->title = Yii::t('app', 'Beche-De-Mer transport  Application');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Applicationtransportbechedemers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <div class="applicationtransportbechedemer-create">

                <h1><?= Html::encode($this->title) ?></h1>

                <?= $this->render('_form', [
                    'model' => $model,
                    'storePlaces' => $storePlaces,

                ]) ?>

            </div>
        </div>
    </div>
</div>
