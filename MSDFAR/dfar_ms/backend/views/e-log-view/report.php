<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use kartik\export\ExportMenu;

$this->title = 'E-Log Records';
?>

<?php
$exportMenu = [
    [
        'attribute' => 'vessel_id',
        'label' => 'Vessel ID',
    ],
    [
        'attribute' => 'skipper_id',
        'label' => 'Skipper ID',
    ],
    [
        'attribute' => 'arrival_date',
        'label' => 'Arrival Date',
    ],
    [
        'attribute' => 'departure_date',
        'label' => 'Departure Date',
    ],
    [
        'attribute' => 'arrival_harbour_name',
        'label' => 'Arrival Harbour',
        'value' => function ($model) {
            return $model->arrivalHarbour->Name ?? 'N/A';
        },
    ],
    [
        'attribute' => 'departure_harbour_name',
        'label' => 'Departure Harbour',
        'value' => function ($model) {
            return $model->departureHarbour->Name ?? 'N/A';
        },
    ],
    [
        'attribute' => 'log_sheet_number',
        'label' => 'Log Book No',
    ],

    [
        'attribute' => 'log_book_no',
        'label' => 'Log Page No',
    ],
];
?>

<div class="container-fluid mt-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 fw-bold">
            <span class="text-primary">⚓</span> E-Log Records
        </h4>
        <span class="badge bg-secondary fs-6"><?= $dataProvider->totalCount ?> record(s) found</span>
    </div>

    <!-- ═══════════════════════════════════════════
         FILTER PANEL
    ════════════════════════════════════════════ -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center"
            style="cursor:pointer" data-bs-toggle="collapse" data-bs-target="#filterPanel">
            <span><i class="bi bi-funnel-fill me-2"></i>Search &amp; Filter</span>
            <i class="bi bi-chevron-down"></i>
        </div>

        <div class="collapse show" id="filterPanel">
            <?php $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['report'],
                'options' => ['class' => 'card-body'],
            ]); ?>

            <div class="row g-3">

                <!-- Vessel ID -->
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'vessel_id')
                        ->textInput(['placeholder' => 'e.g. SL-1234', 'class' => 'form-control form-control-sm'])
                        ->label('Vessel ID') ?>
                </div>

                <!-- Skipper ID -->
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'skipper_id')
                        ->textInput(['placeholder' => 'Skipper ID', 'class' => 'form-control form-control-sm'])
                        ->label('Skipper ID') ?>
                </div>

                <!-- Log Sheet No -->
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'log_sheet_number')
                        ->textInput(['placeholder' => 'Log Sheet #', 'class' => 'form-control form-control-sm'])
                        ->label('Log Sheet No.') ?>
                </div>

                <!-- Log Book No -->
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'log_book_no')
                        ->textInput(['placeholder' => 'Log Book #', 'class' => 'form-control form-control-sm'])
                        ->label('Log Book No.') ?>
                </div>

                <!-- Arrival Harbour -->
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'arrival_harbour_name')
                        ->textInput(['placeholder' => 'Arrival harbour name', 'class' => 'form-control form-control-sm'])
                        ->label('Arrival Harbour') ?>
                </div>

                <!-- Departure Harbour -->
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'departure_harbour_name')
                        ->textInput(['placeholder' => 'Departure harbour name', 'class' => 'form-control form-control-sm'])
                        ->label('Departure Harbour') ?>
                </div>

                <!-- Approval Status -->
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'approve')
                        ->dropDownList(
                            ['' => '— All —', '1' => 'Approved', '0' => 'Pending'],
                            ['class' => 'form-select form-select-sm']
                        )
                        ->label('Approval Status') ?>
                </div>

            </div><!-- /.row -->

            <!-- Date range row -->
            <hr class="my-3">
            <div class="row g-3">

                <!-- Arrival Date From -->
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'arrival_date_from')
                        ->input('date', ['class' => 'form-control form-control-sm'])
                        ->label('Arrival Date — From') ?>
                </div>

                <!-- Arrival Date To -->
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'arrival_date_to')
                        ->input('date', ['class' => 'form-control form-control-sm'])
                        ->label('Arrival Date — To') ?>
                </div>

                <!-- Departure Date From -->
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'departure_date_from')
                        ->input('date', ['class' => 'form-control form-control-sm'])
                        ->label('Departure Date — From') ?>
                </div>

                <!-- Departure Date To -->
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'departure_date_to')
                        ->input('date', ['class' => 'form-control form-control-sm'])
                        ->label('Departure Date — To') ?>
                </div>

            </div><!-- /.row (dates) -->

            <div class="d-flex gap-2 mt-3">
                <?= Html::submitButton('<i class="bi bi-search me-1"></i>Search', [
                    'class' => 'btn btn-primary btn-sm px-4'
                ]) ?>
                <?= Html::a('<i class="bi bi-x-circle me-1"></i>Clear', ['report'], [
                    'class' => 'btn btn-outline-secondary btn-sm'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
    
    <div class = "mb-4">
         <?= ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $exportMenu,
                'exportConfig' => [
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_HTML => false,
                    ExportMenu::FORMAT_EXCEL => false,
                ],
                'dropdownOptions' => [
                    'label' => 'Export All',
                    'class' => 'btn btn-outline-secondary btn-default'
                ]
            ]);
            ?>
    </div>
    <!-- ═══════════════════════════════════════════
         RESULTS TABLE
    ════════════════════════════════════════════ -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-nowrap">#</th>
                            <th class="text-nowrap">Log Book</th>
                            <th class="text-nowrap">Log Page</th>
                            <th class="text-nowrap">Vessel ID</th>
                            <th class="text-nowrap">Skipper ID</th>
                            <th class="text-nowrap">Departure Harbour</th>
                            <th class="text-nowrap">Departure Date</th>
                            <th class="text-nowrap">Arrival Harbour</th>
                            <th class="text-nowrap">Arrival Date</th>
                            <th class="text-nowrap text-center">Status</th>
                            <th class="text-nowrap text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dataProvider->models)): ?>
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                    No e-log records match your search.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($dataProvider->models as $i => $eLog): ?>
                                <tr>
                                    <td class="text-muted small">
                                        <?= ($dataProvider->pagination->page * $dataProvider->pagination->pageSize) + $i + 1 ?>
                                    </td>
                                    <td><?= Html::encode($eLog->log_sheet_number ?? '—') ?></td>
                                    <td><?= Html::encode($eLog->log_book_no ?? '—') ?></td>
                                    <td class="fw-semibold"><?= Html::encode($eLog->vessel_id) ?></td>
                                    <td><?= Html::encode($eLog->skipper_id) ?></td>
                                    <td><?= Html::encode($eLog->departureHarbour->Name ?? '—') ?></td>
                                    <td class="text-nowrap">
                                        <?= $eLog->departure_date
                                            ? date('d M Y', strtotime($eLog->departure_date))
                                            : '—' ?>
                                    </td>
                                    <td><?= Html::encode($eLog->arrivalHarbour->Name ?? '—') ?></td>
                                    <td class="text-nowrap">
                                        <?= $eLog->arrival_date
                                            ? date('d M Y', strtotime($eLog->arrival_date))
                                            : '—' ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($eLog->approve == 1): ?>
                                            <span class="badge bg-success">Approved</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <?= Html::a('View', ['view', 'id' => $eLog->id], ['class' => 'btn btn-sm btn-outline-primary', 'title' => 'View']) ?>
                                        <?= Html::a('PDF', ['export-pdf', 'id' => $eLog->id], ['class' => 'btn btn-sm btn-outline-danger', 'title' => 'Export PDF']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($dataProvider->pagination->pageCount > 1): ?>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Showing
                    <?= ($dataProvider->pagination->page * $dataProvider->pagination->pageSize) + 1 ?>
                    –
                    <?= min(
                        ($dataProvider->pagination->page + 1) * $dataProvider->pagination->pageSize,
                        $dataProvider->totalCount
                    ) ?>
                    of <?= $dataProvider->totalCount ?> records
                </small>
                <?= LinkPager::widget([
                    'pagination' => $dataProvider->pagination,
                    'options' => ['class' => 'pagination pagination-sm mb-0'],
                    'linkOptions' => ['class' => 'page-link'],
                    'pageCssClass' => 'page-item',
                    'activePageCssClass' => 'page-item active',
                    'disabledPageCssClass' => 'page-item disabled',
                    'prevPageLabel' => '‹',
                    'nextPageLabel' => '›',
                    'firstPageLabel' => '«',
                    'lastPageLabel' => '»',
                ]) ?>
            </div>
        <?php endif; ?>
    </div>

</div><!-- /.container-fluid -->