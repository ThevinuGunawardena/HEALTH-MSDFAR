<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Applicationexportnakla $model */

$this->title = Yii::t('app', 'NAKLA export Application');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Export naklas License'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    body {
        background-image: url('/dfar_ms/backend/web/themeAssets/images/background.jpeg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 250px;
        background-color: rgba(0, 0, 0, 0.8);
        color: #fff;
        padding: 20px;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.5);
    }

    .sidebar a {
        color: #fff;
        text-decoration: none;
        display: block;
        margin: 35px 0;
        padding: 0px;
        border-radius: 5px;
        transition: background-color 0.3s;
    }

    .sidebar a:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }

    .content {
        margin-left: 0px;
        padding: 0px;
    }
</style>


<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <h1><?= Html::encode($this->title) ?></h1>

            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>

        </div>
    </div>
</div>
