<?php

use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var backend\models\Inquiry $model */

$this->title = $model->Name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Inquiries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);


$this->registerCss("

");
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="inquiry-view">

                <!-- <h1><?= Html::encode($this->title) ?></h1> -->

                <?php Pjax::begin(['enablePushState' => false, 'timeout' => 5000]); ?>

                <p>
                    <?= Html::a(Yii::t('app', 'Update'), ['update', 'Inquiry_ID' => $model->Inquiry_ID], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'Inquiry_ID' => $model->Inquiry_ID], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                            'method' => 'post',
                        ],
                    ]) ?>
                </p>

                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => array_filter([
                        'Name',
                        'phone_number',
                        'Email:email',
                        [
                            'attribute' => 'District',
                            'value' => function ($model) {
                            return $model->district0 ? $model->district0->name : null;
                        },
                        ],
                        'Office',
                        'Inquiry_Type',
                        'Description:ntext',
                        'ip_address',
                        [
                            'attribute' => 'Submission_Date',
                            'format' => 'datetime',
                            'value' => function ($model) {
                                if ($model->Submission_Date) {
                                    $date = new \DateTime($model->Submission_Date);
                                    return $date->format('Y-m-d H:i:s');
                                }
                                return null;
                            },
                        ],
                        'Inquiry_Status',
                        [
                            'attribute' => 'completion_date',
                            'format' => 'datetime',
                            'value' => function ($model) {
                                if ($model->completion_date) {
                                    $date = new \DateTime($model->completion_date);
                                    return $date->format('Y-m-d H:i:s');
                                }
                                return null;
                            },
                            'visible' => !empty($model->completion_date),
                        ],
                        // Conditionally show 'remarks' only for users with type 15
                        !Yii::$app->user->isGuest && in_array(Yii::$app->user->identity->type, [15, 16]) ? [
                            'attribute' => 'remarks',
                            'format' => 'text',
                        ] : null,
                        [
                            'label' => 'Attachment',
                            'format' => 'raw',
                            'visible' => $model->file !== null, // Only show this field if there's a file
                            'value' => function ($model) {
                            if ($model->file && $model->file->file_name) {
                                return Html::a(
                                    '<i class="fas fa-download"></i> Download File',
                                    ['download', 'id' => $model->Inquiry_ID],
                                    [
                                        'class' => 'btn btn-primary btn-sm',
                                        'target' => '_blank',
                                        'data-pjax' => '0',
                                        'title' => 'Download ' . $model->file->file_name
                                    ]
                                );
                            }
                            return null; // Return null so nothing is displayed
                        }
                        ],
                    ]),
                ]) ?>
                <?php Pjax::end(); ?>
            </div>
        </div>
    </div>
</div>