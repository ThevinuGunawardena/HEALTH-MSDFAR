<?php

use backend\config\Constant;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\BoatDesign $model */

$this->title = $model->design_notation;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Boat Designs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
$webURL = Yii::getAlias('@web');

?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h1><?= Html::encode($this->title) ?></h1>

            <p>
                <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]) ?>
            </p>
            <hr>
            <div class="row">
                <div class="col-lg-12">
                    <h3>Uploaded files</h3>
                </div>
                <div class="col-lg-12">
                    <?php
                    //             foreach ($files as $file) { ?>
                    <!--                <li>-->
                    <?php //= $file->fileType->discription ?><!-- : <a target="_blank" href="-->
                    <?php //=$webURL?><!--/uploads/files/--><?php //= $file->file_name ?><!--">-->
                    <?php //= $file->file_name ?><!--</a></li>-->
                    <!--            --><?php //}
                    ?>
                </div>
            </div>

            <hr>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'yard',
                        'format' => 'text',
                        'label' => 'Yard',
                        'value' => function ($model) {
                            return $model->yard0->name;
                        }
                    ],
                    [
                        'attribute' => 'boat_type',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->boatType->code;
                        }
                    ],
                    [
                        'attribute' => 'hull_material',
                        'format' => 'text',
                        'label' => 'Hull material',
                        'value' => function ($model) {
                            return Constant::$hullMaterials[$model->hull_material] ?? "";
                        }
                    ],
                    [
                        'attribute' => 'engin_type',
                        'format' => 'text',
                        'label' => 'Engin type',
                        'value' => function ($model) {
                            return Constant::$engineTypes[$model->engin_type] ?? "";
                        }
                    ],
                    [
                        'attribute' => 'fi_district',
                        'format' => 'text',
                        'label' => 'District',
                        'value' => function ($model) {
                            return $model->fiDistrict->name;
                        }
                    ],
                    'design_notation',
                    'length',
                    'width',
                    'height',
                    'draft',
                    'remark',
                    'status',
                ],
            ]) ?>

        </div>
    </div>
</div>
