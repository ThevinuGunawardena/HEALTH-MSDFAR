<?php

use yii\helpers\Html;
use backend\config\Constant;

$this->title = Yii::t('app', 'Progress Report');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <?php if (!Yii::$app->request->get('pdf')): ?>
        <div class="card shadow-sm mb-5">
            <div class="card-body">
                <?= Html::a('Download PDF', ['download-progress-report', 'pdf' => 1], [
                    'class' => 'btn btn-success',
                    'target' => '_blank'
                ]) ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<!-- PAGE 1 -->
<div class="license-view">
    <div class="page">

        <img src="<?= Constant::$BASEURL_LICENSE ?>Steve_Report_Front_Page.jpg"
             style="
                position:absolute;
                top:0;
                left:0;
                width:210mm;
                height:297mm;
                display:block;
             ">

    </div>
</div>