<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\BankBranches $model */

$this->title = Yii::t('app', 'Create Bank Branches');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bank Branches'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bank-branches-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
