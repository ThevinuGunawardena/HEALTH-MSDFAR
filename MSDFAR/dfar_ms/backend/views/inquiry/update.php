<?php

use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var backend\models\Inquiry $model */

$this->title = Yii::t('app', 'Update Inquiry: {name}', [
    'name' => $model->Name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Inquiries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'Inquiry_ID' => $model->Inquiry_ID]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="inquiry-update">

                <?php Pjax::begin(['enablePushState' => false, 'timeout' => 5000]); ?>

                <h1><?= Html::encode($this->title) ?></h1>

                <!-- Render form here, which contains ActiveForm inside -->
                <?= $this->render('_form', ['model' => $model]) ?>

                <?php Pjax::end(); ?>

            </div>
        </div>
    </div>
</div>
