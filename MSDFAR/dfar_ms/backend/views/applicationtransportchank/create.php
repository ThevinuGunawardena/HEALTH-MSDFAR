<?php

/** @var yii\web\View $this */
/** @var backend\models\Applicationtransportchank $model */

$this->title = Yii::t('app', 'Chank  transport  Application');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'CHANK transport license'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <div class="applicationtransportchank-create">


                <?= $this->render('_form', [
                    'model' => $model,
                    'storePlaces' => $storePlaces,
                ]) ?>

            </div>
        </div>
    </div>
</div>
