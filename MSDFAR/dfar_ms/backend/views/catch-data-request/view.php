<?php

use yii\helpers\Html;
use backend\config\Constant;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\Url;
use backend\models\BoatNumbers;
use backend\models\MHarbours;
use backend\models\MMainGearTypes;
use backend\models\CatchDataFishcatch;

/** @var yii\web\View $this */
/** @var backend\models\CatchDataRequest $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Catch Data Requests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$CatchDataFishcatchModel = new \backend\models\CatchDataFishcatch();
$userType = Yii::$app->user->identity->type;
?>


    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <!-- <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?> -->
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'landing_date',
           [
                'attribute' => 'boat_registration_id',
                'format' => 'text',
                'label' => 'Boat Number',
                'value' => function ($model) {
                    $boatNumber = \backend\models\BoatNumbers::findOne($model->boat_registration_id);
                    return $boatNumber ? $boatNumber->boat_number : 'N/A';
                }
            ],
            [
                'attribute' => 'unloading_harbour',
                'format' => 'text',
                'label' => 'Unloading Harbour',
                'value' => function ($model) {
                    $unloading_harbour = \backend\models\MHarbours::findOne($model->unloading_harbour);
                    return $unloading_harbour ? $unloading_harbour->Name : 'N/A';
                }
            ], 
             [
                'attribute' => 'fishing_gear_type',
                'format' => 'text',
                'label' => 'Main Gear type',
                'value' => function ($model) {
                    return Constant::$exportGearTypes[$model->fishing_gear_type] ?? 'N/A';

                }
            ],     
            'log_book_no',
            'log_book_page_no',
            // 'created_at',
            // 'created_by',
        ],
    ]) ?>

<?php if ($userType != 226): ?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin([
        'action' => ['catch-data-fishcatch/create'],
        'method' => 'post',
    ]); ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Fish Type</th>
                <th>Total Number of Fish According to Log Book</th>
                <th>Total Catch According to Log Book (KG)</th>
                <th>Actual Number of Fish</th>
                <th>Actual Weight Of Catch</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>
                    <?= $form->field($CatchDataFishcatchModel, 'fish_type')
                        ->dropDownList(Constant::$exportFishTypes, [
                            'prompt' => 'Select Fish Type'
                        ])
                        ->label(false) ?>
                </td>

                <td><?= $form->field($CatchDataFishcatchModel, 'num_of_fish_log')->textInput()->label(false) ?></td>

                <td><?= $form->field($CatchDataFishcatchModel, 'weight_of_fish_log')->textInput()->label(false) ?></td>

                <td><?= $form->field($CatchDataFishcatchModel, 'num_of_fish_act')->textInput([
                    'onchange' => "
                        let log = parseFloat(document.getElementById('catchdatafishcatch-num_of_fish_log').value);
                        let act = parseFloat(this.value);

                        if (!isNaN(log) && !isNaN(act)) {
                            let min = log * 0.9;
                            let max = log * 1.1;

                            if (act < min || act > max) {
                                alert('Value must be within ±10% of Fish Count in Log Book (' + min.toFixed(2) + ' - ' + max.toFixed(2) + ')');
                                this.value = '';
                            } else {
                                document.getElementById('catchdatafishcatch-remain_fish_count').value = act;
                            }
                        }
                    "
                ])->label(false) ?>
                </td>

                <td>
                    <?= $form->field($CatchDataFishcatchModel, 'weight_of_fish_act')
                        ->textInput([
                            'onchange' => "
                                document.getElementById('catchdatafishcatch-remain_weight').value = this.value;
                            "
                        ])
                        ->label(false) ?>
                </td>

                <td>
                    <?= Html::submitButton('+', ['class' => 'btn btn-success']) ?>
                </td>
            </tr>
        </tbody>
    </table>

    <?= $form->field($CatchDataFishcatchModel, 'remain_weight')
        ->hiddenInput(['id' => 'catchdatafishcatch-remain_weight'])
        ->label(false) ?>

    <?= $form->field($CatchDataFishcatchModel, 'remain_fish_count')
        ->hiddenInput(['id' => 'catchdatafishcatch-remain_fish_count'])
        ->label(false) ?>

    <?= $form->field($CatchDataFishcatchModel, 'catchdata_req_id')
        ->hiddenInput(['value' => $model->id])
        ->label(false) ?>

    <?php ActiveForm::end(); ?>


<?php endif; ?>


     <?= GridView::widget([
        'dataProvider' => $fishcatchsearchModeldataProvider,
        'showFooter' => true,
        // 'filterModel' => $fishcatchsearchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'fish_type',
                'value' => function ($model) {
                    return \backend\config\Constant::$exportFishTypes[$model->fish_type] ?? 'N/A';
                }
            ],    
            // 'num_of_fish_log',
            // 'weight_of_fish_log',
            // 'num_of_fish_act',
            // 'weight_of_fish_act',


            [
                        'attribute' => 'num_of_fish_log',
                        'format' => 'text',
                        'label' => 'Total Number of Fish According to Log Book',
                        'value' => function ($model) {
                            return $model->num_of_fish_log;
                        }
                    ],
            [
                        'attribute' => 'weight_of_fish_log',
                        'format' => 'text',
                        'label' => 'Total Catch According to Log Book (KG)',
                        'value' => function ($model) {
                            return $model->weight_of_fish_log . ' KG';
                        },
                        'footer' => '<b>Total Accoding To Log Book: ' . ($totalLog ?? 0) . ' KG</b>',
                    ],
            [
                        'attribute' => 'num_of_fish_act',
                        'format' => 'text',
                        'label' => 'Actual Number of Fish',
                        'value' => function ($model) {
                            return $model->num_of_fish_act;
                        }
                    ],
           [
                    'attribute' => 'weight_of_fish_act',
                    'format' => 'text',
                    'label' => 'Actual Weight Of Catch',
                    'value' => function ($model) {
                        return $model->weight_of_fish_act . ' KG';
                    },
                    'footer' => '<b>Total Of Actual Weight: ' . ($totalActual ?? 0) . ' KG</b>',
            ],
            //'catchdata_req_id',
           [
                'class' => \yii\grid\ActionColumn::class,
                 'visible' => Yii::$app->user->identity->type != 226, 
                'template' => '{remove}',

                'buttons' => [
                    'remove' => function ($url, $model) {
                        return \yii\helpers\Html::a(
                            'x',
                            $url,
                            [
                                'class' => 'btn btn-danger btn-sm',
                                'data' => [
                                    'confirm' => 'Are you sure you want to delete this item?',
                                    'method' => 'post',
                                ],
                            ]
                        );
                    },
                ],

                'urlCreator' => function ($action, $model) {
                    return \yii\helpers\Url::to([
                        'catch-data-fishcatch/delete', // 🔥 FIXED
                        'id' => $model->id
                    ]);
                },
            ],
        ],
    ]); ?>

  <?php if ($userType != 226): ?>

<div class="d-flex justify-content-end">
    <?= Html::button('Submit', [
        'class' => 'btn btn-success',
        'id' => 'catch-request-submit-btn'
    ]) ?>
</div>

<?php endif; ?>


<?php if ($userType == 226): ?>

<div class="container-fluid px-0"> <!-- 🔥 remove side padding -->

    <div class="row m-0"> <!-- 🔥 remove row margin -->
        <div class="col-12 p-0"> <!-- 🔥 remove column padding -->

            <hr>

            <?php if (!empty($purcheseDeatils)): ?>

                <h4 class="mt-3 px-3">Fish Purchese Breakdown</h4>

                <div class="table-responsive"> <!-- 🔥 important -->
                    <table class="table table-bordered mb-0" style="width:100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fish Type</th>
                                <th>Number Of Fish</th>
                                <th>Weight Of Fish</th>
                                <th>Exporter</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $i = 1; foreach ($purcheseDeatils as $row): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= \backend\config\Constant::$exportFishTypes[$row->fish_type] ?? 'N/A' ?></td>
                                    <td><?= $row->No_of_Fish ?? 0 ?></td>
                                    <td><?= $row->Weight_of_Fish ?? 0 ?> KG</td>
                                    <td>
                                        <?= isset($row->user->officerProfile)
                                            ? $row->user->officerProfile->first_name . ' ' . $row->user->officerProfile->last_name
                                            : 'N/A' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php else: ?>
                <p class="text-danger mt-3 px-3">No fish purchese breakdown data found</p>
            <?php endif; ?>

        </div>
    </div>

</div>

<?php endif; ?>

<script>
document.getElementById('catchdatafishcatch-weight_of_fish_act')
    .addEventListener('input', function () {

        let value = this.value;

        document.getElementById('catchdatafishcatch-remain_weight').value = value;
});
</script>