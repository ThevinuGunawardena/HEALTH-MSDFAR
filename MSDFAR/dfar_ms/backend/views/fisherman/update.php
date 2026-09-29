<?php

/** @var yii\web\View $this */
/** @var backend\models\ProfileFisherman $model */

$this->title = Yii::t(
    'app',
    'Update Profile Fisherman: {name}',
    [
        'name' =>
            (string) $model->fisherman_uid
            . ' '
            . (string) $model->first_name
            . ' '
            . (string) $model->last_name,
    ]
);

$this->params['breadcrumbs'][] = [
    'label' => Yii::t(
        'app',
        'Profile Fishermen'
    ),
    'url' => [
        'index',
    ],
];

$this->params['breadcrumbs'][] = [
    'label' => $this->title,
    'url' => [
        'view',
        'id' => $model->id,
    ],
];

$this->params['breadcrumbs'][] =
    Yii::t('app', 'Update');

?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <?= $this->render(
                '_form',
                [
                    'model' => $model,
                    'districtList' =>
                        $districtList ?? [],
                    'divisionList' =>
                        $divisionList ?? [],
                    'years' => $years ?? [],
                    'renew' => false,
                    'isUpdateForm' => true,
                    'token' => $token,

                ]
            ) ?>
        </div>
    </div>
</div>