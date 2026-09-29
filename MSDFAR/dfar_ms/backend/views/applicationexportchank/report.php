<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Applicationexportchank $model */

$issueDate = new DateTime();
// $expiryDate = (clone $issueDate)->modify('+3 months');
$this->title = 'Licence for ' . Html::encode($model->full_name);
$this->params['breadcrumbs'][] = ['label' => 'Applicationexportchank', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="applicationexportchank-report" id="report-content" style="border: 2px solid #000; padding: 20px; margin: 20px; background-color: #f9f9f9;">

    <h1 style="text-align: center;">DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES</h1>
    <br>
    <p style="text-align: center;">
        P.O.Box 531, New Secretariat Maligawatta, Colombo 10, Sri Lanka<br>
        Telephone: 0094-11-2449170, 2422980, 2434075<br>
        Fax: 0094-11-2449170, 2422980, 2434075<br>
        Email: dgfar@gmail.com
    </p>

    <h3 style="text-align: center;">Licence for Export of CHANK</h3>
    <br>

    <h4>Applicant Information</h4>
    <table class="table table-bordered" style="width: 100%;">
        <tr>
            <td style="width: 50%;"><strong>Full Name:</strong></td>
            <td style="width: 50%;"><?= Html::encode($model->full_name) ?></td>
        </tr>
        <tr>
            <td><strong>Address:</strong></td>
            <td><?= Html::encode($model->address) ?></td>
        </tr>
    </table>

    <h4>CHANK Export Details</h4>
    <table class="table table-bordered" style="width: 100%;">
        <tr>
            <td style="width: 50%;"><strong>Commercial Name:</strong></td>
            <td style="width: 50%;"><?= Html::encode($model->commercial_name) ?></td>
        </tr>
        <tr>
            <td><strong>Size (mm):</strong></td>
            <td><?= Html::encode($model->size) ?></td>
        </tr>
        <tr>
            <td><strong>Weight in Kilograms:</strong></td>
            <td><?= Html::encode($model->weight_kg) ?></td>
        </tr>
        <tr>
            <td><strong>Number of Pieces:</strong></td>
            <td><?= Html::encode($model->weight_pieces) ?></td>
        </tr>
        <tr>
            <td><strong>From where caught:</strong></td>
            <td><?= Html::encode($model->caught_place) ?></td>
        </tr>
        <tr>
            <td><strong>Export Countries:</strong></td>
            <td><?= Html::encode($model->export_countries) ?></td>
        </tr>
    </table>

    <p>According to your request dated <?= Html::encode($model->request_date) ?>, this Department has no objection of transporting and keeping in possession of CHANK subjected to the following conditions.</p>
    <ol>
        <li>Add content</li>
        <li>Add content</li>
    </ol>

    <h4>Issue Date</h4>
    <p><strong>Issue Date:</strong> <?= date('Y-m-d') ?></p>

    <p style="text-align: right;">
        <strong>E-signature:</strong><br>
        <strong>Director General <br>
        Department of Fisheries & Aquatic Resources</strong>
    </p>

</div>

<!-- Position the download button outside of the report content -->
<div style="text-align: left; margin: 20px;">
    <?= Html::button('Download Licence', ['class' => 'btn btn-primary', 'onclick' => 'downloadReportAsImage()']) ?>
</div>
