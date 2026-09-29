<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Applicationexportlobster $model */

$issueDate = new DateTime();
// $expiryDate = (clone $issueDate)->modify('+3 months');
$this->title = 'Licence for ' . Html::encode($model->full_name);
$this->params['breadcrumbs'][] = ['label' => 'Applicationexportlobster', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="applicationexportlobster-report" id="report-content" style="border: 2px solid #000; padding: 20px; margin: 20px; background-color: #f9f9f9;">

    <h1 style="text-align: center;">DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES</h1>
    <br>
    <p style="text-align: center;">
        P.O.Box 531, New Secretariat Maligawatta, Colombo 10, Sri Lanka<br>
        Telephone: 0094-11-2449170, 2422980, 2434075<br>
        Fax: 0094-11-2449170, 2422980, 2434075<br>
        Email: dgfar@gmail.com
    </p>

    <h3 style="text-align: center;">Licence for Export Lobster</h3>
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
        <tr>
            <td><strong>Telephone Number:</strong></td>
            <td><?= Html::encode($model->telephone) ?></td>
        </tr>
    </table>

    <h4>Lobster Export Details</h4>
    <table class="table table-bordered" style="width: 100%;">
        <tr>
            <td style="width: 50%;"><strong>Lobster Species:</strong></td>
            <td style="width: 50%;"><?= Html::encode($model->lobster_species) ?></td>
        </tr>
        <tr>
            <td><strong>Number of Lobsters:</strong></td>
            <td><?= Html::encode($model->number) ?></td>
        </tr>
        <tr>
            <td><strong>Weight (kg):</strong></td>
            <td><?= Html::encode($model->weight) ?></td>
        </tr>
        <tr>
            <td><strong>Caught Place:</strong></td>
            <td><?= Html::encode($model->caught_place) ?></td>
        </tr>
        <tr>
            <td><strong>Export Countries:</strong></td>
            <td><?= Html::encode($model->export_countries) ?></td>
        </tr>
    </table>

    <p>According to your request dated <?= Html::encode($model->request_date) ?>, this Department has no objection of transporting and keep in possession of 
    Lobster subjected to the following conditions.</p>
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

<!-- Include html2canvas library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
<script>
    function downloadReportAsImage() {
        html2canvas(document.getElementById('report-content'), {
            onrendered: function(canvas) {
                var link = document.createElement('a');
                link.href = canvas.toDataURL('image/png');
                link.download = 'report.png';
                link.click();
            }
        });
    }
</script>