<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Applicationtransportlobster $model */

$this->title = Yii::t('app', 'Lobster transport Application');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Lobster transport Application'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="applicationtransportlobster-create">


                <?= $this->render('_form', [
                    'model' => $model,
                    'storePlaces' => $storePlaces,
                ]) ?>

            </div>
        </div>
    </div>
</div>
