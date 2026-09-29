<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\FishermanDependant $model */

$this->title = Yii::t('app', 'Create Fisherman Dependant');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Fisherman Dependants'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fisherman-dependant-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
