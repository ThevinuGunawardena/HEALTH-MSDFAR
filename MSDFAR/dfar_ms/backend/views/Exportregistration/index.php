<?php

use backend\models\Exportregistration;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var backend\models\ExportregistrationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Exportregistrations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="exportregistration-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Exportregistration'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'application_name',
            'address',
            'mobile',
            'fax',
            'email:email',
            //'reg_no',
            //'id',
            //'permit_type',
            //'commercial_name',
            //'quantity_unit',
            //'total_weight',
            //'total_number',
            //'area',
            //'charge',
            //'country_export',
            //'information:ntext',
            //'document',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Exportregistration $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
