<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\MFishTypes $model */

$this->title = Yii::t('app', 'Create M Fish Types');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'M Fish Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mfish-types-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
