<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Importregistration $model */

$this->title = Yii::t('app', 'Import Registration Application');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Importregistrations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="importregistration-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
