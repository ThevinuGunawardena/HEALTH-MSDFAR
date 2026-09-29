<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\ScientificEnumerationRequest $model */

$this->title = Yii::t('app', 'Create Scientific Enumeration Request');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Scientific Enumeration Requests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scientific-enumeration-request-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
