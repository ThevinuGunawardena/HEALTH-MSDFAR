<?php

use backend\config\Constant;
use backend\controllers\FishermanController;
use kartik\export\ExportMenu;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\ProfileFishermanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Fishermen Profiles');
$this->params['breadcrumbs'][] = $this->title;

$exportMenu =
    [
        'fisherman_uid',
        'first_name',
        'last_name',
        'nic',
        //'passport',
        //'dob',
        // 'gender',
        //'permanent_address',
        //'current_address',
        //'blood_group',
        // 'mobile',
        //'fixed_line',
        //'email:email',
        [
            'attribute' => 'district',
            'format' => 'text',
            'label' => 'District',
            'value' => function ($model) {
                return $model->district0->name;
            }
        ],
        [
            'attribute' => 'division',
            'format' => 'text',
            'label' => 'Division',
            'value' => function ($model) {
                return $model->division0->name;
            }
        ],
        [
            'attribute' => 'landing_site',
            'format' => 'text',
            'label' => 'Landing site',
            'value' => function ($model) {
                return $model->landingSite->name ?? "";
            }
        ],
        //'year_recruitment',
        //'life_isurance_no',
        //'member_fisheries_society',
        //'civil',
        //'category',
        //'management_area',
        // [
        //     'attribute' => 'status',
        //     'format' => 'text',
        //     'value' => function ($model) {
        //         return Constant::$licenseStatus[$model->status];
        //     }
        // ],
        'created',
        'expire_date',
    ];

$gridColumns =
    [
        // Add the checkbox column here
        
        'fisherman_uid',
        'first_name',
        'last_name',
        'nic',
        //'passport',
        //'dob',
        // 'gender',
        //'permanent_address',
        //'current_address',
        //'blood_group',
        // 'mobile',
        //'fixed_line',
        //'email:email',
        //'district',
        //'division',
        //'landing_site',
        //'year_recruitment',
        //'life_isurance_no',
        //'member_fisheries_society',
        //'civil',
        //'category',
        //'management_area',
        // 'created:dateTime',
        // 'approved_time',
        'expire_date:date',
        // [
        //     'attribute' => 'status',
        //     'format' => 'text',
        //     'value' => function ($model) {
        //         return Constant::$licenseStatus[$model->status];
        //     }
        // ],
        [
            'attribute' => 'approval_stage',
            'format' => 'text',
            'value' => function ($model) {
                return Constant::$userTypes[$model->approval_stage]['name'] ?? $model->approval_stage;
            }
        ],
        // [
        //     'attribute' => 'Action',
        //     'format' => 'raw',
        //     'value' => function ($model) {
        //         return '<a href="view?id=' . $model->id . '" class="btn btn-sm btn-primary">View</a>';
        //     }
        // ],
        [
    'attribute' => 'Action',
    'format' => 'raw',
    'value' => function ($model) {

        if ($model->printed == 1 || $model->printed == 2) {
        return '<span class="text-success">Already Printed</span>';
    }
        // Check if this print job is already in the queue for the current user
        $isInQueue = \backend\models\IdPrintQueue::find()
                        ->where(['print_id' => $model->id, 'user_id' => Yii::$app->user->identity->id, 'status' => 1])
                        ->exists();  // Returns true if the print job is already in the queue for the current user with status 1
        if ($isInQueue) {
            // If already in queue, show a message
            return '<span class="text-danger">Already added to the queue</span>';
        } else {
            // If not in queue, show the "Add To Printer Queue" button
            return '<a href="' . Yii::$app->urlManager->createUrl(['fisherman/addprintqueue', 'id' => $model->id]) . '" class="btn btn-sm btn-primary">Add To Printer Queue</a>';
        }
    }
],
    ];
?>

<?php echo $this->render('_search_print', ['model' => $searchModel]); ?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h3>Printer Queue</h3>

            <?php
// Fetch the print queue details for the logged-in user
$queueItems = \backend\models\IdPrintQueue::find()
    ->where(['user_id' => Yii::$app->user->identity->id, 'status' => 1])
    ->all(); // Get all queue items for the current user

// Check if there are any records in the queue
if (!empty($queueItems)) {
    // Display the queue items in a table
    echo '<table class="table table-striped">';
    echo '<thead><tr>';
    echo '<th>#</th><th>Fisherman NIC</th><th>Status</th><th>Action</th>';
    echo '</tr></thead>';
    echo '<tbody>';

    // Loop through each queue item and display its details
    foreach ($queueItems as $index => $item) {
        // Use the print_id to fetch the corresponding fisherman NIC from profile_fisherman
        $fisherman = \backend\models\ProfileFisherman::findOne($item->print_id);

        // In the loop where you display each queue item:
        echo '<tr>';
        echo '<td>' . ($index + 1) . '</td>';

        // Display the NIC (if the fisherman record exists)
        echo '<td>' . ($fisherman ? $fisherman->nic : 'N/A') . '</td>';

       echo '<td>';
echo '<span class="' . ($item->status == 1 ? 'badge badge-success' : 'badge badge-warning') . '">';
echo ($item->status == 1 ? 'Ready To Print' : 'Expired - ' . abs(round((strtotime('now') - strtotime($item->expire_date)) / 86400)) . ' Days ago');
echo '</span>';
echo '</td>';
        echo '<td>';
        // Ensure the URL for the "Cancel" button has the correct id parameter
        echo '<a href="' . Yii::$app->urlManager->createUrl(['fisherman/cancelprintqueue', 'id' => $item->id]) . '" class="btn btn-sm btn-danger">Remove From Queue</a>';
        echo '</td>';
        echo '</tr>';
            }

    echo '</tbody>';
    echo '</table>';
} else {
    // If no items in the queue, show a message
    echo '<p>No print jobs in the queue.</p>';
}
?>
    <a href="sendtoprint" class="btn btn-primary float-right">Send To Print<a>

        </div>
    </div>
</div>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <?= GridView::widget([
                'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],
                'dataProvider' => $dataProvider,
                'columns' => $gridColumns
            ]); ?>
        </div>
    </div>
</div>
