<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;

$this->title = $model->first_name . ' ' . $model->last_name;
$this->params['breadcrumbs'][] = ['label' => 'Profile Officers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);

$profileId = $model->id;
$this->registerJsFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', ['depends' => [JqueryAsset::class]]);
?>
<div class="col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <!--            <h1 class="mb-4">--><?php //= Html::encode($this->title) ?><!--</h1>-->

            <div class=" mb-4">
                <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

                <?php if (UserTypeUtil::hasType(Constant::ADMINISTRATION) && $profileId != Yii::$app->user->identity->profile_id) {
                    echo $msdfarEnabled ? Html::a('Disable MSFDAR Access', ['disable-msdfar', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to disable MSFDAR for this profile officer?',
                            'method' => 'post',
                            'bs-toggle' => 'tooltip',
                            'bs-title' => 'Disables MSFDAR system access for this officer',
                        ],
                    ]) : Html::a('Enable MSFDAR Access', ['enable-msdfar', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to enable MSFDAR for this profile officer?',
                            'method' => 'post',
                            'bs-toggle' => 'tooltip',
                            'bs-title' => 'Enables MSFDAR system access for this officer',
                        ],
                    ]);
                } else {
                    echo Html::a(Yii::t('app', 'Update Password'), ['officer/password-update'], ['class' => 'btn btn-warning']);

                } ?>
                <?= Html::a('Back to List', ['index'], ['class' => 'btn btn-secondary']) ?>
            </div>

            <?php if (Yii::$app->user->identity->type != Constant::ADMIN && empty($model->agreement)) : ?>
                <div class="alert alert-warning" role="alert">
                    <strong>🚨 Please upload the agreement soft copy to activate the profile</strong>
                </div>
            <?php endif; ?>

            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal"
                            type="button" role="tab" aria-controls="personal" aria-selected="true">Personal Info
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents"
                            type="button" role="tab" aria-controls="documents" aria-selected="false">Documents
                        and Files
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="exam-tab" data-bs-toggle="tab" data-bs-target="#exam" type="button"
                            role="tab" aria-controls="exam" aria-selected="false">Exam/Training
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="language-tab" data-bs-toggle="tab" data-bs-target="#language"
                            type="button" role="tab" aria-controls="language" aria-selected="false">Language
                        Qualifications
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="promotions-tab" data-bs-toggle="tab" data-bs-target="#promotions"
                            type="button" role="tab" aria-controls="promotions" aria-selected="false">Promotions
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="salary-tab" data-bs-toggle="tab" data-bs-target="#salary" type="button"
                            role="tab" aria-controls="salary" aria-selected="false">Salary Details
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="profileTabContent">
                <!-- Personal Info Tab -->
                <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                    <div class="row">
                        <div class="col-lg-2 col-md-3 col-sm-12 text-center">
                            <img src="<?= Constant::$FILE_VIEW_PATH ?>officer/profile/<?= $model->profile_image ?? 'avatar-1.png' ?>"
                                 alt="Profile Picture"
                                 class="profile-img mb-3"
                                 loading="lazy">
                        </div>
                        <div class="col-lg-10 col-md-9 col-sm-12">
                            <h3 class="mb-3">
                                <?= strtoupper(Html::encode($model->first_name)) ?>
                                <?= strtoupper(Html::encode($model->last_name)) ?>
                                <span class=""><?= Constant::$USER_RANKS_LABEL[$model->user_level] ?? '' ?></span>
                            </h3>
                            <hr>
                            <div class="row">
                                <div class="col-lg-6">
                                    <ul class="list-unstyled">
                                        <li><strong>NIC:</strong> <?= Html::encode($model->nic ?? 'Not available') ?>
                                        </li>
                                        <li>
                                            <strong>Email:</strong> <?= Html::encode($model->personal_email ?? 'Not available') ?>
                                        </li>
                                        <li><strong>Contact
                                                Number:</strong> <?= Html::encode($model->mobile_phone ?? 'Not available') ?>
                                        </li>
                                        <li><strong>Home
                                                Phone:</strong> <?= Html::encode($model->home_phone ?? 'Not available') ?>
                                        </li>
                                        <li><strong>Date of Birth:</strong> <?= Html::encode($model->date_of_birth ??
                                                'Not available') ?></li>
                                        <li><strong>Place of Birth:</strong> <?= Html::encode($model->place_of_birth
                                                ?? 'Not available') ?></li>
                                        <li><strong>Permanent Address:</strong> <?= Html::encode
                                            ($model->permanent_address
                                                ?? 'Not available') ?></li>
                                        <li>
                                            <strong>Rank:</strong> <?= Constant::$USER_RANKS[$model->user_level] ?? 'Not available' ?>
                                        </li>
                                        <li><strong>W and OP Number
                                                :</strong> <?= $model->w_op_number ?? 'Not available' ?></li>
                                        <?php if (Yii::$app->user->identity->type != Constant::DG && Yii::$app->user->identity->type != Constant::DM) : ?>
                                            <li>
                                                <strong>District:</strong> <?= Html::encode($model->district0->name ?? 'Not available') ?>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (Yii::$app->user->identity->type == Constant::FI) : ?>
                                            <li>
                                                <strong>Division:</strong> <?= Html::encode($model->division0->name ?? 'Not available') ?>
                                            </li>
                                        <?php endif; ?>
                                        <li><strong>Status of Appointment
                                                :</strong> <?= $model->appointment_status ?? 'Not available' ?></li>
                                    </ul>
                                </div>
                                <div class="col-lg-6">
                                    <ul class="list-unstyled">
                                        <li><strong>Current
                                                Designation:</strong> <?= Html::encode($model->current_designation ?? 'Not available') ?>
                                        </li>
                                        <li><strong>Current Workplace
                                                Type:</strong> <?= Html::encode($model->current_workplace_type ?? 'Not available') ?>
                                        </li>
                                        <li><strong>Current
                                                Workplace:</strong> <?= Html::encode($model->current_workplace ?? 'Not available') ?>
                                        </li>
                                        <li><strong>District (If
                                                applicable):</strong> <?= Html::encode($model->district0->name ?? 'Not available') ?>
                                        <li><strong>Division (If
                                                applicable):</strong> <?= Html::encode($model->division0->name ?? 'Not 
                                        available') ?>
                                        <li><strong>Harbour (If
                                                applicable):</strong> <?= Html::encode($model->harbour ?? 'Not 
                                        available') ?>
                                        </li>
                                        </li>
                                        <li><strong>Appointment Date for Public Service:</strong> <?= Html::encode
                                            ($model->public_service_appointment_date ?? 'Not available') ?></li>
                                        <li><strong>Appointment Date to DFAR:</strong> <?= Html::encode
                                            ($model->dfar_appointment_date ?? 'Not available') ?></li>
                                        <li><strong>Method of Recruitment to Current Service:</strong> <?= Html::encode
                                            ($model->recruitment_method ?? 'Not available') ?></li>
                                        <li><strong>Device Serial:</strong> <?= Html::encode
                                            ($model->device_serial ?? 'Not available') ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents Tab -->
                <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
                    <div class="row">
                        <div class="col-lg-6">
                            <ul class="list-unstyled">
                                <li><strong>Officer
                                        Agreement:</strong> <?= $model->agreement ? Html::a('View', Constant::$FILE_VIEW_PATH . 'officer/agreement/' . $model->agreement, ['target' => '_blank', 'class' => 'text-primary']) : 'Not available' ?>
                                </li>
                                <li><strong>IT Evaluation Result
                                        Sheet:</strong> <?= $model->it_result_sheet ? Html::a('View', Constant::$FILE_VIEW_PATH . 'officer/cetificate/' . $model->it_result_sheet, ['target' => '_blank', 'class' => 'text-primary']) : 'Not available' ?>
                                </li>
                                <li>
                                    <strong>Certificate:</strong> <?= $model->cetificate ? Html::a('View', Constant::$FILE_VIEW_PATH . 'officer/cetificate/' . $model->cetificate, ['target' => '_blank', 'class' => 'text-primary']) : 'Not available' ?>
                                </li>
                                <li><strong>Appointment
                                        Letter:</strong> <?= $model->appointment_letter ? Html::a('View', Constant::$FILE_VIEW_PATH . 'officer/appointment_letter/' . $model->appointment_letter, ['target' => '_blank', 'class' => 'text-primary']) : 'Not available' ?>
                                </li>
                            </ul>
                        </div>
                        <div class="col-lg-6">
                            <ul class="list-unstyled">
                                <li><strong>DFAR Appointment
                                        Letter:</strong> <?= $model->dfar_appointment_letter ? Html::a('View', Constant::$FILE_VIEW_PATH . 'officer/dfar_appointment_letter/' . $model->dfar_appointment_letter, ['target' => '_blank', 'class' => 'text-primary']) : 'Not available' ?>
                                </li>
                                <li><strong>A certified copy of the valid passport [if
                                        available]:</strong> <?= $model->passport_copy ? Html::a('View', Constant::$FILE_VIEW_PATH . 'officer/passport_copy/' . $model->passport_copy, ['target' => '_blank', 'class' => 'text-primary']) : 'Not available' ?>
                                </li>
                                <li><strong>A certified copy of the valid driving license [if
                                        available]:</strong> <?= $model->driving_license_copy ? Html::a('View', Constant::$FILE_VIEW_PATH . 'officer/driving_license_copy/' . $model->driving_license_copy, ['target' => '_blank', 'class' => 'text-primary']) : 'Not available' ?>
                                </li>
                                <li>
                                    <strong>Photograph:</strong> <?= $model->photograph ? Html::a('View', Constant::$FILE_VIEW_PATH . 'officer/photograph/' . $model->photograph, ['target' => '_blank', 'class' => 'text-primary']) : 'Not available' ?>
                                </li>
                            </ul>
                        </div>
                        <div class="col-lg-4">
                            <ul class="list-unstyled">
                                <li><strong>Signature:</strong><br>
                                    <?= $model->signature ?
                                        Html::img(Constant::$FILE_VIEW_PATH . 'officer/signature/' . $model->signature, [
                                            'style' => 'max-width: 150px; height: auto;',
                                            'alt' => 'Signature',
                                            'class' => 'profile-img mt-2',
                                            'loading' => 'lazy'
                                        ]) : 'Not available' ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Exam/Training Tab -->
                <div class="tab-pane fade" id="exam" role="tabpanel" aria-labelledby="exam-tab">
                    <div class="mb-3">
                        <?= Html::a('Add Exam/Training', ['exam-training/create', 'profile_officer_id' => $model->id], ['class' => 'btn btn-success']) ?>
                    </div>
                    <?= GridView::widget([
                        'dataProvider' => $examTrainingDataProvider,
                        'tableOptions' => ['class' => 'table table-striped '],
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            'exam_training_name',
                            'exam_training_year',
                            'exam_training_institute',
                            [
                                'attribute' => 'results_certificate',
                                'format' => 'raw',
                                'value' => function ($exam) {
                                    return $exam->results_certificate ? Html::a('View', Constant::$FILE_VIEW_PATH . 'exam-training/' . $exam->results_certificate, ['target' => '_blank', 'class' => 'text-primary']) : 'N/A';
                                },
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'controller' => 'exam-training',
                                'template' => '{update} {delete}',
                                'buttons' => [
                                    'update' => function ($url, $exam) use ($profileId) {
                                        return Html::a('Update', ['exam-training/update', 'id' => $exam->id, 'profile_officer_id' => $profileId], ['class' => 'btn btn-secondary btn-sm']);
                                    },
                                    'delete' => function ($url, $exam) use ($profileId) {
                                        return Html::a('Delete', ['exam-training/delete', 'id' => $exam->id, 'profile_officer_id' => $profileId], [
                                            'class' => 'btn btn-danger btn-sm',
                                            'data' => [
                                                'confirm' => 'Are you sure you want to delete this item?',
                                                'method' => 'post',
                                            ],
                                        ]);
                                    },
                                ],
                            ],
                        ],
                    ]) ?>
                </div>

                <!-- Language Qualifications Tab -->
                <div class="tab-pane fade" id="language" role="tabpanel" aria-labelledby="language-tab">
                    <div class="mb-3">
                        <?= Html::a('Add Language Qualification', ['language-qualification/create', 'profile_officer_id' => $model->id], ['class' => 'btn btn-success']) ?>
                    </div>
                    <?= GridView::widget([
                        'dataProvider' => $languageQualificationDataProvider,
                        'tableOptions' => ['class' => 'table table-striped'],
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            'language',
                            'type',
                            'year',
                            [
                                'attribute' => 'results_certificate',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return $model->results_certificate ? Html::a('View', Constant::$FILE_VIEW_PATH . 'officer/language_qualification/' . $model->results_certificate, ['target' => '_blank', 'class' => 'text-primary']) : 'N/A';
                                },
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'controller' => 'language-qualification',
                                'template' => '{update} {delete}',
                                'buttons' => [
                                    'update' => function ($url, $model) use ($profileId) {
                                        return Html::a('Update', ['language-qualification/update', 'id' => $model->id, 'profile_officer_id' => $profileId], ['class' => 'btn btn-secondary btn-sm']);
                                    },
                                    'delete' => function ($url, $model) use ($profileId) {
                                        return Html::a('Delete', ['language-qualification/delete', 'id' => $model->id, 'profile_officer_id' => $profileId], [
                                            'class' => 'btn btn-danger btn-sm',
                                            'data' => [
                                                'confirm' => 'Are you sure you want to delete this item?',
                                                'method' => 'post',
                                            ],
                                        ]);
                                    },
                                ],
                            ],
                        ],
                    ]) ?>
                </div>

                <!-- Promotions Tab -->
                <div class="tab-pane fade" id="promotions" role="tabpanel" aria-labelledby="promotions-tab">
                    <div class="mb-3">
                        <?= Html::a('Add Promotion', ['promotion/create', 'profile_officer_id' => $model->id], ['class' => 'btn btn-success']) ?>
                    </div>
                    <?= GridView::widget([
                        'dataProvider' => $promotionDataProvider,
                        'tableOptions' => ['class' => 'table table-striped'],
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            'promoted_as',
                            'promotion_date:date',
                            [
                                'attribute' => 'promotion_letter',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return $model->promotion_letter ? Html::a('View', Constant::$FILE_VIEW_PATH . 'officer/promotion_letter/' . $model->promotion_letter, ['target' => '_blank', 'class' => 'text-primary']) : 'N/A';
                                },
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'controller' => 'promotion',
                                'template' => '{update} {delete}',
                                'buttons' => [
                                    'update' => function ($url, $model) use ($profileId) {
                                        return Html::a('Update', ['promotion/update', 'id' => $model->id, 'profile_officer_id' => $profileId], ['class' => 'btn btn-secondary btn-sm']);
                                    },
                                    'delete' => function ($url, $model) use ($profileId) {
                                        return Html::a('Delete', ['promotion/delete', 'id' => $model->id, 'profile_officer_id' => $profileId], [
                                            'class' => 'btn btn-danger btn-sm',
                                            'data' => [
                                                'confirm' => 'Are you sure you want to delete this item?',
                                                'method' => 'post',
                                            ],
                                        ]);
                                    },
                                ],
                            ],
                        ],
                    ]) ?>
                </div>

                <!-- Salary Details Tab -->
                <div class="tab-pane fade" id="salary" role="tabpanel" aria-labelledby="salary-tab">
                    <div class="mb-3">
                        <?= Html::a('Add Salary', ['salary/create', 'profile_officer_id' => $model->id], ['class' => 'btn btn-success']) ?>
                    </div>
                    <?= GridView::widget([
                        'dataProvider' => $salaryDataProvider,
                        'tableOptions' => ['class' => 'table table-striped'],
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            'current_basic_salary:currency',
                            'salary_increment_date:date',
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'controller' => 'salary',
                                'template' => '{update} {delete}',
                                'buttons' => [
                                    'update' => function ($url, $model) use ($profileId) {
                                        return Html::a('Update', ['salary/update', 'id' => $model->id, 'profile_officer_id' => $profileId], ['class' => 'btn btn-secondary btn-sm']);
                                    },
                                    'delete' => function ($url, $model) use ($profileId) {
                                        return Html::a('Delete', ['salary/delete', 'id' => $model->id, 'profile_officer_id' => $profileId], [
                                            'class' => 'btn btn-danger btn-sm',
                                            'data' => [
                                                'confirm' => 'Are you sure you want to delete this item?',
                                                'method' => 'post',
                                            ],
                                        ]);
                                    },
                                ],
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* assets/css/profile.css */
    /*.card {*/
    /*    border: none;*/
    /*    border-radius: 8px;*/
    /*    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);*/
    /*    transition: transform 0.2s ease;*/
    /*}*/

    /*.card:hover {*/
    /*    transform: translateY(-2px);*/
    /*}*/

    /*.card-header {*/
    /*    background-color: #f8f9fa;*/
    /*    border-bottom: 1px solid #e9ecef;*/
    /*    padding: 1rem 1.5rem;*/
    /*    font-size: 1.25rem;*/
    /*    font-weight: 500;*/
    /*    color: #343a40;*/
    /*}*/

    /*.card-body {*/
    /*    padding: 1.5rem;*/
    /*}*/

    /*.btn {*/
    /*    border-radius: 6px;*/
    /*    padding: 0.5rem 1rem;*/
    /*    font-weight: 500;*/
    /*    transition: all 0.3s ease;*/
    /*}*/

    /*.btn-primary {*/
    /*    background-color: #007bff;*/
    /*    border-color: #007bff;*/
    /*}*/

    /*.btn-primary:hover {*/
    /*    background-color: #0056b3;*/
    /*    border-color: #004085;*/
    /*}*/

    /*.btn-danger {*/
    /*    background-color: #dc3545;*/
    /*    border-color: #dc3545;*/
    /*}*/

    /*.btn-danger:hover {*/
    /*    background-color: #c82333;*/
    /*    border-color: #bd2130;*/
    /*}*/

    /*.btn-secondary {*/
    /*    background-color: #6c757d;*/
    /*    border-color: #6c757d;*/
    /*}*/

    /*.btn-success {*/
    /*    background-color: #28a745;*/
    /*    border-color: #28a745;*/
    /*}*/

    .profile-img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        border: 1px solid #e9ecef;
        padding: 0.25rem;
        display: block;
        margin: 0 auto;
    }

    /*.alert-warning {*/
    /*    background-color: #fff3cd;*/
    /*    border-color: #ffeeba;*/
    /*    color: #856404;*/
    /*    border-radius: 6px;*/
    /*    padding: 1rem;*/
    /*    font-weight: 500;*/
    /*}*/

    .list-unstyled li {
        margin-bottom: 0.75rem;
        font-size: 1rem;
        color: #495057;
    }

    .list-unstyled li strong {
        color: #212529;
    }

    /*.table {*/
    /*    border-radius: 6px;*/
    /*    overflow: hidden;*/
    /*}*/

    .table th, .table td {
        vertical-align: middle;
    }

    @media (max-width: 768px) {
        .profile-img {
            max-width: 150px;
        }

        .card-header h3 {
            font-size: 1.1rem;
        }

        .btn {
            padding: 0.4rem 0.8rem;
            font-size: 0.9rem;
        }
    }
</style>