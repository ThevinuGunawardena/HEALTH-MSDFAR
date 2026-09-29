<?php

use backend\models\Importregistration;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var backend\models\ImportregistrationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Importregistrations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="importregistration-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Importregistration'), ['create'], ['class' => 'btn btn-success']) ?>
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
            'fixed_number',
            'email:email',
            //'id',
            //'business_reg_no',
            //'permit_type',
            //'commercial_name',
            //'total_weight',
            //'imported_country',
            //'charge',
            //'information:ntext',
            //'document',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Importregistration $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
