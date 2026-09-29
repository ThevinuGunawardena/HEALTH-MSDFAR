<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Reexport $model */

$this->title = Yii::t('app', 'Re-Export Registration Application');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Reexports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reexport-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
