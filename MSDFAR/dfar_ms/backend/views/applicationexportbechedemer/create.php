<?php

/** @var yii\web\View $this */
/** @var backend\models\Applicationexportbechedemer $model */

$this->title = Yii::t('app', 'Beche-de-mer Export Application');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Export beche-de-mers License'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-xl-10 col-lg-10 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <div class="row">
                <div class="col-lg-12">

                    <?= $this->render('_form', [
                        'model' => $model,
                        'consignments' => $consignments,
                        'create' => true,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>