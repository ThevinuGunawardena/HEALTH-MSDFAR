<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Applicationtransportchank $model */

$issueDate = new DateTime();
$expiryDate = (clone $issueDate)->modify('+6 months');
$this->title = 'Licence for ' . Html::encode($model->full_name);
$this->params['breadcrumbs'][] = ['label' => 'Applicationtransportchank', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="applicationtransportchank-report" id="report-content" style="border: 2px solid #000; padding: 20px; margin: 20px; background-color: #f9f9f9;">

    <h1 style="text-align: center;">DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES</h1>
    <br>
    <p style="text-align: center;">
        P.O.Box 531, New Secretariat Maligawatta, Colombo 10, Sri Lanka<br>
        Telephone: 0094-11-2449170, 2422980, 2434075<br>
        Fax: 0094-11-2449170, 2422980, 2434075<br>
        Email: dgfar@gmail.com
    </p>

    <h3 style="text-align: center;">Licence for Transport of CHANK</h3>
    <br>

    <h4>Applicant Information</h4>
    <table class="table table-bordered" style="width: 100%;">
        <tr>
            <td style="width: 50%;"><strong>Full Name:</strong></td>
            <td style="width: 50%;"><?= Html::encode($model->full_name) ?></td>
        </tr>
        <tr>
            <td><strong>Permanent Address:</strong></td>
            <td><?= Html::encode($model->permanent_address) ?></td>
        </tr>
        <tr>
            <td><strong>NIC Number:</strong></td>
            <td><?= Html::encode($model->national_id) ?></td>
        </tr>
        <tr>
            <td><strong>Business Registration Number:</strong></td>
            <td><?= Html::encode($model->business_registration_no) ?></td>
        </tr>
    </table>

    <h4>CHANK Transport Details</h4>
    <table class="table table-bordered" style="width: 100%;">
        <tr>
            <td style="width: 50%;"><strong>purchasing District:</strong></td>
            <td style="width: 50%;"><?= !empty($model->purchasing_district) ? Html::encode(implode(', ', (array)$model->purchasing_district)) : "Not provided" ?></td>
        </tr>
        <tr>
            <td><strong>Total Weight:</strong></td>
            <td><?= !empty($model->total_weight) ? Html::encode(implode(', ', (array)$model->total_weight)) : "Not provided" ?></td>  
        </tr>
        <tr>
            <td><strong>Intermediate Destination</strong></td>
            <td><?= !empty($model->intermediate_destination) ? Html::encode(implode(', ', (array)$model->intermediate_destination)) : "Not provided" ?></td>  
        </tr>
        <tr>
            <td><strong>Final Destination:</strong></td>
            <td><?= !empty($model->final_destination) ? Html::encode(implode(', ', (array)$model->final_destination)) : "Not provided" ?></td>  
        </tr>
        <tr>
            <td><strong>Vehicle:</strong></td>
            <td><?= Html::encode($model->vehicle) ?></td>
        </tr>
        <tr>
            <td><strong>Boat:</strong></td>
            <td><?= Html::encode($model->boat) ?></td>
        </tr>
    </table>

    <p>According to your request dated <?= Html::encode($model->request_date) ?>, this Department has no objection on transporting 
        of “CHANK” subjected to the following conditions.</p>

    <ol>
        <li>The total quantity of the Consignments should not exceed 2500 Kgs.</li>
        <li>You are liable to provide particulars of transports done using this permit.</li>
        <li>This letter of no objection if not cancelled early is, valid only for
            six months’ period from <?= $issueDate->format('Y-m-d') ?> <?= $expiryDate->format('Y-m-d') ?></li>
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