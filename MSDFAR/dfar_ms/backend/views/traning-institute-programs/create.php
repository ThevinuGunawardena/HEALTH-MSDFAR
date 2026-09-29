<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\MTraningInstitutePrograms $model */

$this->title = Yii::t('app', 'Create M Traning Institute Programs');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'M Traning Institute Programs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mtraning-institute-programs-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
