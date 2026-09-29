<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\MFiDistrict $model */

$this->title = Yii::t('app', 'Create M Fi District');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'M Fi Districts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mfi-district-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
