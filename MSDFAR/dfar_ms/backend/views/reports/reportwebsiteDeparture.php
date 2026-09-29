<?php

use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\SkipperSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Licence and other important details of IMUL boats operated by Sri Lanka');
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="row">
                <div class="col-xl-12 mb-5">
                    <center>
                        <h2>Departure Cancel boat list
                        </h2>
                        <!--                        <p>(Please be noted that licenses issued via MSDFAR application are listed below)</p>-->
                    </center>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">

                    <?php $form = ActiveForm::begin([
                            'method' => 'get',
                            'action' => ["departure-status"],
                            'options' => [
                                    'data-pjax' => 1
                            ],
                    ]); ?>

                    <div class="row">

                        <div class="col-xl-6">
                            <div class="form-group field-reportSearch-from">
                                <label class="control-label" for="reportSearch-from">Boat number</label>
                                <input type="text" id="reportSearch-boat" class="form-control" name="boat"
                                       value="<?= $boat
                                       ?>">
                            </div>
                        </div>
                        <!--                        <div class="col-xl-6">-->
                        <!--                            <div class="form-group field-reportSearch-to">-->
                        <!--                                <label class="control-label" for="reportSearch-to">Status</label>-->
                        <!--                                --><?php //= Html::dropDownList(
                        //                                        'status',
                        //                                        $status,
                        //                                        Constant::getMainTypesKeyValue("DROPDOWN"),
                        //                                        [
                        //                                                'id' => 'status',
                        //                                                'class' => 'form-control',
                        //                                                'prompt' => 'Select Status'
                        //                                        ]
                        //                                ) ?>
                        <!--                            </div>-->
                        <!--                        </div>-->


                    </div>
                    <div class="row">
                        <div class="col-xl-6 mt-3">
                            <div class="form-group">
                                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                                <?= Html::a('Reset', ['departure-status'], ['class' => 'btn btn-outline-secondary']) ?>
                            </div>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <p style="font-weight: bold">
                        * <span class="text-danger">Red</span> text indicates licenses that have expired or will expire
                        within
                        30
                        days.
                    </p>
                    <?= GridView::widget([
                            'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],
//                'filterModel' => $searchModel,
                            'dataProvider' => $dataProvider,

                            'columns' => [
//                    ['class' => 'yii\grid\SerialColumn'],

//                    'id',
                                    [
                                            'attribute' => 'boat_number_id',
                                            'format' => 'text',
                                            'value' => function ($model) {
                                                return $model->boat->boat_number;
                                            }
                                    ],
                                    [
                                            'attribute' => 'fisherman_id',
                                            'format' => 'text',
                                            'value' => function ($model) {
                                                return $model->fisherman->preferred_name_for_id ?? 'N/A';
                                            }
                                    ],
                                    [
                                            'label' => 'NIC',
                                            'format' => 'text',
                                            'value' => function ($model) {
                                                return $model->fisherman->nic ?? 'N/A';
                                            }
                                    ],
                                    [
                                            'label' => 'Mobile',
                                            'format' => 'text',
                                            'value' => function ($model) {
                                                return $model->fisherman->mobile ?? 'N/A';
                                            }
                                    ],
                                    [
                                            'attribute' => 'status',
                                           'contentOptions' => function ($model) {

    if ($model->compulsory_service == 0) {

        // Violation + compulsory service pending
        if (!empty($model->status) && $model->status != 'Departure Allowed') {
            return ['style' => 'background-color:#ff7474;color:white'];
        }

        // Only compulsory service pending
        return ['style' => 'background-color:#ffff91;'];
    }

    if ($model->status == 'Departure Allowed' || $model->status == '') {
        return ['style' => 'background-color:#55c355;color:white'];
    }

    if ($model->status != 'Departure Allowed' && $model->status != '') {
        return ['style' => 'background-color:#ff7474;color:white'];
    }

    return [];
},

'value' => function ($model) {

    if ($model->compulsory_service == 0) {

        // Violation case
        if (!empty($model->status) && $model->status != 'Departure Allowed') {
            return $model->status . ' (Compulsory Service Pending)';
        }

        // Only compulsory service pending
        return 'Compulsory Service Pending';
    }

    // Normal display
    return $model->status == '' ? 'Departure Allowed' : $model->status;
}

                                    ],

                                    [
//                        'attribute' => 'latest_highseas_expire',
                                            'label' => 'Expire date boat registration',
//                        'format'    => 'date',   // still works — null dates become empty by default
                                            'value' => function ($model) {
                                                return $model->latestBoatRegExpire ?: null;
                                                // or more explicit:
                                                // return $model->latest_highseas_expire ? Yii::$app->formatter->asDate($model->latest_highseas_expire) : 'NA';
                                            },
                                            'contentOptions' => function ($model) {

                                                $date = $model->latestBoatRegExpire;

                                                if (!$date) {
                                                    return ['class' => 'text-muted']; // no date
                                                }

                                                $time = strtotime($date);
                                                $today = time();
                                                $daysLeft = floor(($time - $today) / 86400);

                                                if ($time < $today) {
                                                    return ['class' => 'text-danger']; // expired
                                                } elseif ($daysLeft <= 30) {
                                                    return ['class' => 'text-danger']; // within 30 days
                                                } else {
                                                    return ['class' => 'text-success']; // safe
                                                }
                                            },
                                    ],
                                    [
//                        'attribute' => 'latest_highseas_expire',
                                            'label' => 'Expire date EEZ licence',
//                        'format'    => 'date',   // still works — null dates become empty by default
                                            'value' => function ($model) {
                                                return $model->latestNationalExpire ?: null;
                                                // or more explicit:
                                                // return $model->latest_highseas_expire ? Yii::$app->formatter->asDate($model->latest_highseas_expire) : 'NA';
                                            },
                                            'contentOptions' => function ($model) {

                                                $date = $model->latestNationalExpire;

                                                if (!$date) {
                                                    return ['class' => 'text-muted']; // no date
                                                }

                                                $time = strtotime($date);
                                                $today = time();
                                                $daysLeft = floor(($time - $today) / 86400);

                                                if ($time < $today) {
                                                    return ['class' => 'text-danger']; // expired
                                                } elseif ($daysLeft <= 30) {
                                                    return ['class' => 'text-danger']; // within 30 days
                                                } else {
                                                    return ['class' => 'text-success']; // safe
                                                }
                                            },
                                    ],
                                    [
//                        'attribute' => 'latest_highseas_expire',
                                            'label' => 'Expire date high seas licence',
//                        'format'    => 'date',   // still works — null dates become empty by default
                                            'value' => function ($model) {
                                                return $model->latestHighseasExpire ?: null;
                                                // or more explicit:
                                                // return $model->latest_highseas_expire ? Yii::$app->formatter->asDate($model->latest_highseas_expire) : 'NA';
                                            },
                                            'contentOptions' => function ($model) {

                                                $date = $model->latestHighseasExpire;

                                                if (!$date) {
                                                    return ['class' => 'text-muted']; // no date
                                                }

                                                $time = strtotime($date);
                                                $today = time();
                                                $daysLeft = floor(($time - $today) / 86400);

                                                if ($time < $today) {
                                                    return ['class' => 'text-danger']; // expired
                                                } elseif ($daysLeft <= 30) {
                                                    return ['class' => 'text-danger']; // within 30 days
                                                } else {
                                                    return ['class' => 'text-success']; // safe
                                                }
                                            },
                                    ],

                            ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
