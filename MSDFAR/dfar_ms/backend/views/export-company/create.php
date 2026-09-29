<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\ExportCompany $model */

$this->title = Yii::t('app', 'Create Export Company');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Export Companies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="export-company-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
