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


$exportMenu =
    [
        'boat_number',
        'preferred_name_for_id',
        'nic',
        [
                'attribute' => 'national_license_101',
            'value' => function ($model) {
                return $model->national_license_101 ?? "NA";
            }
        ],
           [
            'attribute' => 'national_approved_101',
            'value' => function ($model) {
                return !empty($model->national_expire_101)
                    ? date_format(
                        date_sub(
                            date_create($model->national_expire_101),
                            date_interval_create_from_date_string("1 year -1 day")
                        ),
                        "Y-m-d"
                    )
                    : "NA";
            }
        ],
        [
                'attribute' => 'national_expire_101',
            'value' => function ($model) {
                return $model->national_expire_101 != null && $model->national_expire_101 != "" ? date_format(date_create
                ($model->national_expire_101), "Y-m-d") : "NA";

            }
        ],
          [
                            'attribute' => 'national_license_403',
                            'value' => function ($model) {
                                return $model->national_license_403 ?? "NA";
                            }
                    ],

                    [
                            'attribute' => 'national_expire_403',
                            'value' => function ($model) {
                                return $model->national_expire_403 != null && $model->national_expire_403 != "" ? date_format
                                (date_create
                                ($model->national_expire_403), "Y-m-d") : "NA";

                            }
                    ],
        [
                'attribute' => 'highseas_license_101',
            'value' => function ($model) {
                return $model->highseas_license_101 ?? "NA";
            }
        ],

            [
                    'attribute' => 'highseas_approved_101',
                    'value' => function ($model) {
                        $expire_date_string = $model->highseas_expire_101 ?? '2026-11-27';

                        $formatted_date = (new DateTime($expire_date_string))
                                ->modify('-1 year')
                                ->modify('-1 day')
                                ->format('Y-m-d');
                        return (!empty($model->highseas_expire_101))
                                ? (new DateTime($model->highseas_expire_101))->modify('-1 year +1 day')->format('Y-m-d')
                                : "NA";
                    }
            ],

        [
                'attribute' => 'highseas_expire_101',
            'value' => function ($model) {
                return $model->highseas_expire_101 != null && $model->highseas_expire_101 != "" ? date_format(date_create
                ($model->highseas_expire_101), "Y-m-d") : "NA";

            }
        ],
            [
                    'attribute' => 'highseas_license_403',
                    'value' => function ($model) {
                        return $model->highseas_license_403 ?? "NA";
                    }
            ],

            [
                    'attribute' => 'highseas_expire_403',
                    'value' => function ($model) {
                        return $model->highseas_expire_403 != null && $model->highseas_expire_403 != "" ? date_format
                        (date_create
                        ($model->highseas_expire_403), "Y-m-d") : "NA";

                    }
            ],
        [
            'attribute' => 'call_sign_no',
            'value' => function ($model) {
                return $model->call_sign_no == "" || $model->call_sign_no == "nil" ? "NA" : $model->call_sign_no;
            }
        ],
        [
            'attribute' => 'imo_no',
            'value' => function ($model) {
                return $model->imo_no == "" || $model->imo_no == "nil" ? "NA" : $model->imo_no;
            }
        ],
        [
            'attribute' => 'iotc_record',
            'value' => function ($model) {
                return $model->iotc_record == "" || $model->iotc_record == "nil" ? "NA" : $model->iotc_record;
            }
        ],
        [
            'attribute' => 'mmsi_no_for_ais',
            'value' => function ($model) {
                return $model->mmsi_no_for_ais == "" || $model->mmsi_no_for_ais == "nil" ? "NA" : $model->mmsi_no_for_ais;
            }
        ],
        [
            'attribute' => 'log_book_no',
            'value' => function ($model) {
                return $model->log_book_no == "" || $model->log_book_no == "nil" ? "NA" : $model->log_book_no;
            }
        ],
    ];
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
                        <h2>List of Multiday Fishing Vessels with Valid Fisheries Operation Licenses of Sri Lanka
                        </h2>
                        <p>(Please be noted that licenses issued via MSDFAR application are listed below)</p>
                    </center>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">

                    <?php $form = ActiveForm::begin([
                        'method' => 'get',
                        'action' => ["website"],
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
                        <div class="col-xl-6">
                            <div class="form-group field-reportSearch-to">
                                <label class="control-label" for="reportSearch-to">NIC</label>
                                <input type="text" id="reportSearch-nic" class="form-control" name="nic" value="<?= $nic
                                ?>">
                            </div>
                        </div>


                    </div>
                    <div class="row">
                        <div class="col-xl-6 mt-3">
                            <div class="form-group">
                                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                            </div>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <?= GridView::widget([
                        'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],
                        'dataProvider' => $dataProvider,
                        'columns' => $exportMenu,
                        'summary' => false,
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
