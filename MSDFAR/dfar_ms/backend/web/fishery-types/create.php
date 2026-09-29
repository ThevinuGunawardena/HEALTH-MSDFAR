<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\MFisheryTypes $model */

$this->title = Yii::t('app', 'Create M Fishery Types');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'M Fishery Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mfishery-types-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
