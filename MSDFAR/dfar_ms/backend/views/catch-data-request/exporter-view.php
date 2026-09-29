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

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Catch Data Requests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$CatchDataPurchaseDetailsModel = new \backend\models\CatchDataPurchaseDetails();
?>

<!--  SEARCH SECTION -->

    <div class="col-md-6">
        <label>Log Sheet Number</label>
        <input type="text" class="form-control" id="logBookNo" placeholder="Enter Log Sheet Number">
    </div>

    <div class="col-md-6">
        <label>Log Sheet Page Number</label>
        <input type="text" class="form-control" id="logSheetNo" placeholder="Enter Log Sheet Page Number">
    </div>

    <div class="col-md-12 d-flex justify-content-end mt-3">
        <button type="button" class="btn btn-success" id="searchBtn">Search</button>
    </div>


<hr>

<!--  RESULT TABLE -->
<?php if (!empty($data)): ?>
<h3 style="display:block;">Data of Log Sheet of Boat Number: <?= $boatNumber ?> Landed On <?= $landing_date ?></h3>

<table class="table table-striped mt-4">
    <thead>
        <tr>
            <th>#</th>
            <th>Fish Type</th>
            <th>Number Of Fish</th>
            <th>Weight Of Fish</th>
        </tr>
    </thead>

    <tbody>
        <?php $i = 1; foreach ($data as $row): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td>
                    <?= Constant::$exportFishTypes[$row['fish_type']] ?? 'N/A' ?>
                </td>
                <td><?= $row['total_fish'] ?></td>
                <td><?= $row['total_weight'] ?> KG</td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php endif; ?>

<hr>

<h5 class="mt-4">Purchase Your Quota</h5>

<!--  SECOND TABLE FORM -->
<div class="container-fluid mt-4">

<?php $form = ActiveForm::begin([
    'action' => ['catch-data-purchase-details/create'],
    'method' => 'post',
]); ?>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Fish Type</th>
            <th>Number Of Fish</th>
            <th>Weight Of Fish</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        <tr>
           <td>
    <?= $form->field($CatchDataPurchaseDetailsModel, 'fish_type')
        ->dropDownList(Constant::$exportFishTypes, [
            'prompt' => 'Select Fish Type'
        ])
        ->label(false) ?>
</td>

<td>
    <?= $form->field($CatchDataPurchaseDetailsModel, 'No_of_Fish')
        ->textInput()
        ->label(false) ?>
</td>

<td>
    <?= $form->field($CatchDataPurchaseDetailsModel, 'Weight_of_Fish')
        ->textInput()
        ->label(false) ?>
</td>

<td>
    <?= Html::submitButton('+', ['class' => 'btn btn-success']) ?>
</td>
    </tbody>
</table>
<?= Html::hiddenInput('logBNo', Yii::$app->request->get('logBNo')) ?>
<?= Html::hiddenInput('logSNo', Yii::$app->request->get('logSNo')) ?>
<?php ActiveForm::end(); ?>

</div>


<?php if (!empty($purchaseData)): ?>


<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Fish Type</th>
            <th>Number Of Fish</th>
            <th>Weight Of Fish</th>
            <th>Action</th>

        </tr>
    </thead>

    <tbody>
        <?php $i = 1; foreach ($purchaseData as $row): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td>
                    <?= \backend\config\Constant::$exportFishTypes[$row->fish_type] ?? 'N/A' ?>
                </td>
                <td><?= $row->No_of_Fish ?></td>
                <td><?= $row->Weight_of_Fish ?> KG</td>
                <td>
                    <?= Html::a(
                        'x',
                        ['catch-data-purchase-details/delete', 'id' => $row->id],
                        [
                            'class' => 'btn btn-danger btn-sm',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this item?',
                                'method' => 'post',
                            ],
                        ]
                    ) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>
    <p class="mt-3">No purchase data found</p>
<?php endif; ?>

<table class="table table-borderless">
        <thead>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </thead>

    <tbody>
        <tr>
    <td colspan="6" style="text-align: right;">
        <?= Html::button('Submit Your Request', [
            'class' => 'btn btn-success',
            'id' => 'exporter-catch-request-submit-btn'
        ]) ?>
    </td>
</tr>
    </tbody>
</table>

<!-- 🔥 SCRIPT -->
<script>
document.getElementById('searchBtn').addEventListener('click', function () {

    let logBNo = document.getElementById('logBookNo').value;
    let logSNo = document.getElementById('logSheetNo').value;

    if (!logBNo || !logSNo) {
        alert('Please enter both values');
        return;
    }

    window.location.href = '<?= \yii\helpers\Url::to(["catch-data-request/get-logsheet-data"]) ?>'
        + '?logBNo=' + encodeURIComponent(logBNo)
        + '&logSNo=' + encodeURIComponent(logSNo);
});
</script>