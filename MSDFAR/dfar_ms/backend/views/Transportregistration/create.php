<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Transportregistration $model */

$this->title = Yii::t('app', 'Transport Registration Application');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Transportregistrations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transportregistration-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
