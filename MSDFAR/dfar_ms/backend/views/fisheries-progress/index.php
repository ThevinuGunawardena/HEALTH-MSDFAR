<?php

use backend\models\FisheriesProgress;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var backend\models\FisheriesProgressSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Fisheries Progresses');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fisheries-progress-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
    </p>
     <?php if ($currentMonthRecordCount == 0 || $previousMonthRecordCount == 0) : ?>
        <p>
<?= Html::a(Yii::t('app', 'Create Fisheries Progress Month Record'), '#', [
    'class' => 'btn btn-success',
    'id' => 'create-record-btn',
]) ?>
        </p>
    <?php endif; ?>

 <?php Pjax::begin(); ?>

<div class="row">
    <!-- Loop through the records for the current month -->
    <?php foreach ($currentMonthRecords as $currentMonthRecord) : ?>
        <div class="col-xl-6">
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">Current Month (<?= date('F Y') ?>)</h5>
                    <h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>
                    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                    
                    <!-- View button for the specific record -->
                    <?= Html::a(Yii::t('app', 'View Record'), ['view', 'id' => $currentMonthRecord->id], [
                        'class' => 'btn btn-primary',
                        'data-pjax' => 'false',
                        'onclick' => 'window.location.href = "' . Url::to(['view', 'id' => $currentMonthRecord->id]) . '"; return false;'
                    ]) ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Loop through the records for the previous month -->
    <?php foreach ($previousMonthRecords as $previousMonthRecord) : ?>
        <div class="col-xl-6">
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">Previous Month (<?= date('F Y', strtotime('first day of last month')) ?>)</h5>
                    <h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>
                    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                    
                    <!-- View button for the specific record -->
                    <?= Html::a(Yii::t('app', 'View Record'), ['view', 'id' => $previousMonthRecord->id], [
                        'class' => 'btn btn-primary',
                        'data-pjax' => 'false',
                        'onclick' => 'window.location.href = "' . Url::to(['view', 'id' => $previousMonthRecord->id]) . '"; return false;'
                    ]) ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Show the "Create Fisheries Progress" button only if there are no records for both months -->
   
</div>

<?php Pjax::end(); ?>


</div>
<script>
    $(document).ready(function () {
        $('#create-record-btn').click(function () {
            $.ajax({
                url: '<?= Yii::$app->urlManager->createUrl(['fisheries-progress/create']) ?>', // The URL to call the create action
                type: 'POST',
                success: function (response) {
                    // Redirect to the index page after successful creation
                    window.location.href = '<?= Yii::$app->urlManager->createUrl(['fisheries-progress/index']) ?>';
                },
                error: function () {
                    alert('Error occurred while creating the record');
                }
            });
        });
    });
</script>
