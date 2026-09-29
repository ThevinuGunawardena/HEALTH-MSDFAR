<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Applicationexportlobster $model */

$this->title = Yii::t('app', 'Lobster export Application');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Export lobsters License'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="applicationexportlobster-create">

                <h1><?= Html::encode($this->title) ?></h1>

                <?= $this->render('_form', [
                    'model' => $model,
                    'consignments' => $consignments,
                ]) ?>

            </div>
        </div>
    </div>
</div>
