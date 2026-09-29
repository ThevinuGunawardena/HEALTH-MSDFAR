<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\ScientificData $model */
/** @var string $token */

$this->title = Yii::t('app', 'Update Scientific Data');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Scientific Datas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Scientific Data'),
    'url' => ['/scientific/view', 'token' => $token],
];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="scientific-data-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'token' => $token,
    ]) ?>

</div>
