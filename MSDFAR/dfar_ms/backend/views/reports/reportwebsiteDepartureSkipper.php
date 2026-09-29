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
                        <h2>Departure Cancel skipper list
                        </h2>
                        <!--                        <p>(Please be noted that licenses issued via MSDFAR application are listed below)</p>-->
                    </center>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">

                    <?php $form = ActiveForm::begin([
                            'method' => 'get',
                            'action' => ["departure-skipper-status"],
                            'options' => [
                                    'data-pjax' => 1
                            ],
                    ]); ?>

                    <div class="row">

                        <div class="col-xl-6">
                            <div class="form-group field-reportSearch-from">
                                <label class="control-label" for="reportSearch-from">NIC</label>
                                <input type="text" id="reportSearch-boat" class="form-control" name="nic"
                                       value="<?= $nic
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
                                <?= Html::a('Reset', ['departure-skipper-status'], ['class' => 'btn 
                                btn-outline-secondary']) ?>
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
//                'filterModel' => $searchModel,

                            'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

//            'id',
                                    'skipper_name',
                                    'crew_type',
                                    'nic',
                                    'skipper_id',
                                //'address',
                                //'contact',
                                    'status',
                                //'approved_by',
                                //'timestamp',
                                //'harbor',
                                //'served_vessel',
                                //'dep_date',
                                //'dep_id',
                                //'dep_cancel_allow_by',
                                //'dep_cancel_date',
                                //'to_date:ntext',
//                    'remarks',
                                    'offence_reason',

                            ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
