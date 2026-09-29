<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\AuthItem $model */

$this->title = Yii::t('app', 'Create new Role');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'User roles and Permissions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h1><?= Html::encode($this->title) ?></h1>

            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>

        </div>
    </div>
</div>
