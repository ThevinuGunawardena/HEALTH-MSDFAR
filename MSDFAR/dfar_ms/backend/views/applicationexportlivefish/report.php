<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Applicationexportlivefish $model */

$issueDate = new DateTime();
$expiryDate = (clone $issueDate)->modify('+3 months');
$this->title = 'Licence for ' . Html::encode($model->full_name);
$this->params['breadcrumbs'][] = ['label' => 'Applicationexportlivefish', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="applicationexportlivefish-report" id="report-content" style="border: 2px solid #000; padding: 20px; margin: 20px; background-color: #f9f9f9;">

    <h1 style="text-align: center;">DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES</h1>
    <br>
    <p style="text-align: center;">
        P.O.Box 531, New Secretariat Maligawatta, Colombo 10, Sri Lanka<br>
        Telephone: 0094-11-2449170, 2422980, 2434075<br>
        Fax: 0094-11-2449170, 2422980, 2434075<br>
        Email: dgfar@gmail.com
    </p>

    <h3 style="text-align: center;">Licence for Export Live Fish</h3>
    <br>

    <h4>Applicant Information</h4>
    <table class="table table-bordered" style="width: 100%;">
        <tr>
            <td style="width: 50%;"><strong>Full Name:</strong></td>
            <td style="width: 50%;"><?= Html::encode($model->full_name) ?></td>
        </tr>
        <tr>
            <td><strong>Permanent Address:</strong></td>
            <td><?= Html::encode($model->address) ?></td>
        </tr>
        <tr>
            <td><strong>Business Registration Number:</strong></td>
            <td><?= Html::encode($model->business_reg_number) ?></td>
        </tr>
    </table>

    <h4>Fish Species Details</h4>
    <table class="table table-bordered" style="width: 100%;">
        <tr>
            <td style="width: 50%;"><strong>Species / Type:</strong></td>
            <td style="width: 50%;"><?= !empty($model->species_type) ? Html::encode(implode(', ', (array)$model->species_type)) : "Not provided" ?></td>
        </tr>
        <tr>
            <td><strong>Locally Collected Quantity (kg):</strong></td>
            <td><?= !empty($model->locally_collected_fish_quantity_value) ? Html::encode(implode(', ', (array)$model->locally_collected_fish_quantity_value)) : "Not provided" ?></td>
        </tr>
        <tr>
            <td><strong>Locally Bred Quantity (kg):</strong></td>
            <td><?= !empty($model->locally_bred_fish_quantity_value) ? Html::encode(implode(', ', (array)$model->locally_bred_fish_quantity_value)) : "Not provided" ?></td>
        </tr>
        <tr>
            <td><strong>Re-exported Quantity (kg):</strong></td>
            <td><?= !empty($model->re_exported_fish_quantity_value) ? Html::encode(implode(', ', (array)$model->re_exported_fish_quantity_value)) : "Not provided" ?></td>
        </tr>
        <tr>
            <td><strong>Weight (kg):</strong></td>
            <td><?= !empty($model->weight) ? Html::encode(implode(', ', (array)$model->weight)) : "Not provided" ?></td>
        </tr>
        <tr>
            <td><strong>Export Countries:</strong></td>
            <td><?= !empty($model->export_countries) ? Html::encode(implode(', ', (array)$model->export_countries)) : "Not provided" ?></td>
        </tr>
    </table>

    <p>According to your request dated <?= Html::encode($model->request_date) ?>, this Department has no objection of transporting and keep in possession of 
    Live Fish subjected to the following conditions.</p>
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