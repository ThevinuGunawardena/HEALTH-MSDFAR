<?php

use backend\models\Transportregistration;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var backend\models\TransportregistrationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Transportregistrations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transportregistration-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Transportregistration'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'applicant_name',
            'address',
            'mobile_number',
            'email:email',
            'reg_number',
            //'nid_number',
            //'id',
            //'mail_address',
            //'permit_type',
            //'type',
            //'quantity',
            //'area',
            //'vehicle_number',
            //'destination_district',
            //'product_storing_area',
            //'finalstoreplace_address',
            //'import_country',
            //'document',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Transportregistration $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
