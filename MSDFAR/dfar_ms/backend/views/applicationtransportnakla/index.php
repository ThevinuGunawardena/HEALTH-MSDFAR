<?php

use backend\config\Constant;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\ApplicationtransportnaklaSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Application transport naklas');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Application transport nakla'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],

        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'full_name',
            'permanent_address',
            'telephone',
            'fax_number',
            'nic_number',
            [
                'attribute' => 'approval_stage',
                'format' => 'text',
                'filter' => [Constant::SPECIAL_LICENCE => "SPECIAL_LICENCE", Constant::AD => "AD",
                    Constant::DM => "DM", Constant::DG => "DG"],
                'value' => function ($model) {
                    return Constant::$userTypes[$model->approval_stage]['name'] ?? $model->approval_stage;
                }
            ],
            [
                'attribute' => 'status',
                'format' => 'text',
                'label' => 'Status',
                'filter' => ArrayHelper::toArray(Constant::$licenseStatus),
                'value' => function ($model) {
                    return Constant::$licenseStatus[$model->status] ?? $model->status;
                }
            ],

            [
            'label' => 'Action',
            'format' => 'raw',
            'value' => static function ($model): string {
                return Html::a(
                    Yii::t('app', 'View'),
                    [
                        'view',
                        'id' => (int) $model->id,
                    ],
                    [
                        'class' => 'btn btn-sm btn-primary',
                        'target' => '_blank',
                        'rel' => 'noopener noreferrer',
                    ]
                );
            },
        ],
            
        ],
    ]); ?>


        </div>
    </div>
</div>
