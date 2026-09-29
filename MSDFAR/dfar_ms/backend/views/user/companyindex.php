<?php

use backend\config\Constant;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Export Company Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h1><?= Html::encode($this->title) ?></h1>

            <p>
                <?= Html::a(Yii::t('app', 'Create User'), ['createcompanyuser'], ['class' => 'btn btn-success']) ?>
            </p>

            <!--    --><?php //Pjax::begin(); ?>
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

            <?= GridView::widget([
                'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [

//            'id',
                    'nic',
                    //            'auth_key',
                    //            'password_hash',
                    //            'password_reset_token',
                    'email:email',
                    //'updated_at',
                    //'verification_token',
                    [
                        'attribute' => 'type',
                        'format' => 'text',
                        'filter' => Constant::$userTypes,

                        'value' => function ($model) {

                            return Constant::$userTypes[$model->type]['name'] ?? "-";
                        }
                    ],
                    [
                        'attribute' => 'user_permission',
                        'format' => 'text',
                        'filter' => Constant::$userPermissions,

                        'value' => function ($model) {

                            return Constant::$userPermissions[$model->user_permission] ?? "-";
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'text',
                        'filter' => array(10 => "Active", 9 => "Inactive"),

                        'value' => function ($model) {

                            return $model->status == 10 ? "Active" : "Inactive";
                        }
                    ],
                    [
                        'attribute' => 'Action',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $view =
                                Html::a(Yii::t('app', 'Update Company profile'), ['export-company/update', 'user' =>
                                    $model->id], [
                                    'class' => 'btn btn-sm  btn-info',

                                ]);
                            return '<a href="view?id=' . $model->id . '" class="btn btn-sm btn-primary">View</a> ' .
                                $view;
                        }
                    ],
                ],
            ]); ?>

            <!--    --><?php //Pjax::end(); ?>

        </div>
    </div>
</div>
