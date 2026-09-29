<?php

/** @var yii\web\View $this */
/** @var backend\models\MLandingSite $model */

$this->title = Yii::t('app', 'Create M Landing Site');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Landing Sites'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>

        </div>
    </div>
</div>
