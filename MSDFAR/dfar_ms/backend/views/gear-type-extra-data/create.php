<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\MGearTypeExtraData $model */

$this->title = Yii::t('app', 'Create M Gear Type Extra Data');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'M Gear Type Extra Datas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgear-type-extra-data-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
