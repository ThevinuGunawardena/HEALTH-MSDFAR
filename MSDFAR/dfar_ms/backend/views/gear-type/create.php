<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\MGearTypes $model */

$this->title = Yii::t('app', 'Create M Gear Types');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'M Gear Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgear-types-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
