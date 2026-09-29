<?php

/** @var yii\web\View $this */

/** @var backend\models\ProfileOfficer $model */

$this->title = Yii::t('app', 'Update Profile Office: {name}', [
    'name' => $model->first_name . " " . $model->last_name,
]);
//$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Profile Officers'), 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->first_name, 'url' => ['view', 'id' => $model->id]];
//$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <!--    <div class="card shadow-sm mb-5">-->
    <!--        <div class="card-body">-->
    <!---->
    <!--            --><?php //= Html::a(Yii::t('app', 'Update Password'), ['password-update'], ['class' => 'btn btn-warning']) ?>
    <!---->
    <!--        </div>-->
    <!--    </div>-->
    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>


        </div>
    </div>
</div>
