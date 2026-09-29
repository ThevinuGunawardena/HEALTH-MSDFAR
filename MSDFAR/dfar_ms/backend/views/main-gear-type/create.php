<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\MMainGearTypes $model */

$this->title = Yii::t('app', 'Create M Main Gear Types');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'M Main Gear Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mmain-gear-types-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
