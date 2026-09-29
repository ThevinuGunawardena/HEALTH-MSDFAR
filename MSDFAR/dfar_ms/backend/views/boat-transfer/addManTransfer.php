<?php

use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;

?>

<h2>Add Manual Boat Transfers</h2>

<div class="container-fluid">

<form method="post">

    <!-- CSRF -->
    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" 
           value="<?= Yii::$app->request->csrfToken ?>">

    <div class="row">

        <!-- BOAT -->
        <div class="col-xl-6">
            <label>Boat Number</label>

            <?= Select2::widget([
                'name' => 'boat_number',
                'options' => [
                    'placeholder' => 'Search boat number...',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 2,
                    'ajax' => [
                        'url' => Url::to(['boat-numbers/boat-global-search']),
                        'dataType' => 'json',
                        'delay' => 250,
                        'data' => new JsExpression('function(params) {
                            return { q: params.term };
                        }'),
                        'processResults' => new JsExpression('function(data) {
                            return data;
                        }'),
                    ],
                ],
            ]); ?>
        </div>

        <!-- OWNER -->
        <div class="col-xl-6">
            <label>New Owner</label>

            <?= Select2::widget([
                'name' => 'new_owner',
                'options' => [
                    'placeholder' => 'Search owner (UID / NIC)...',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 2,
                    'ajax' => [
                        'url' => Url::to(['fisherman/search-global']),
                        'dataType' => 'json',
                        'delay' => 250,
                        'data' => new JsExpression('function(params) {
                            return { q: params.term };
                        }'),
                        'processResults' => new JsExpression('function(data) {
                            return data;
                        }'),
                    ],
                ],
            ]); ?>
        </div>

    </div>

<br>
    <div class="row">

        <!-- BOAT -->
        <div class="col-xl-6">
            <label>Transfered Date</label>
            <input type="date" class="form-control" id="transferDate" name="transferDate">
        </div>

        <!-- OWNER -->
        <div class="col-xl-6">
         
        </div>
            

    </div>

    <div class="row mt-3">
        <div class="col-xl-12 text-end">
            <button type="submit" class="btn btn-primary">
                Transfer Boat
            </button>
        </div>
    </div>

</form>

</div>