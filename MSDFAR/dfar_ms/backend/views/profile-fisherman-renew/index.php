<?php

use backend\models\ProfileFishermanRenew;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var backend\models\ProfileFishermanRenewSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Profile Fisherman Renews');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profile-fisherman-renew-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Profile Fisherman Renew'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'fisherman_uid',
            'fisherman_id',
            'district',
            'division',
            //'status',
            //'approval_stage',
            //'created',
            //'approved_time',
            //'expire_date',
            //'renew',
            //'printed',
            //'printed_date',
            //'privacy_policy',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, ProfileFishermanRenew $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
