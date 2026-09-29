<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\MDivision $model */

$this->title = Yii::t('app', 'Create M Division');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'M Divisions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mdivision-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
