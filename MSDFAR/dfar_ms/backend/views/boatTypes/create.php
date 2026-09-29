<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\MBoatTypes $model */

$this->title = Yii::t('app', 'Create M Boat Types');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'M Boat Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mboat-types-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
