<?php

use yii\helpers\Html;

$this->title = 'Create Profile Officer';
$this->params['breadcrumbs'][] = ['label' => 'Profile Officers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <!--    <h1>--><?php //= Html::encode($this->title) ?><!--</h1>-->

            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>

        </div>
    </div>
</div>