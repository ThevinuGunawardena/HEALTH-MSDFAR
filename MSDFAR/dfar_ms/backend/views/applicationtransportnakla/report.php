<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Applicationtransportnakla $model */

$issueDate = new DateTime();
$expiryDate = (clone $issueDate)->modify('+3 months');
$this->title = 'Licence for ' . Html::encode($model->full_name);
$this->params['breadcrumbs'][] = ['label' => 'Applicationtransportnakla', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="applicationtransportnakla-report" id="report-content" style="border: 2px solid #000; padding: 20px; margin: 20px; background-color: #f9f9f9;">

    <h1 style="text-align: center;">DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES</h1>
    <br>
    <p style="text-align: center;">
        P.O.Box 531, New Secretariat Maligawatta, Colombo 10, Sri Lanka<br>
        Telephone: 0094-11-2449170, 2422980, 2434075<br>
        Fax: 0094-11-2449170, 2422980, 2434075<br>
        Email: dgfar@gmail.com
    </p>

    <h3 style="text-align: center;">Licence for Transport and keep in possession of “NAKLA” (operculum of Chicoreous ramosus)</h3>
    <br>

    <h4>Applicant Information</h4>
    <table class="table table-bordered">
        <tr>
            <td style="width: 50%;"><strong>Full Name:</strong></td>
            <td style="width: 50%;"><?= Html::encode($model->full_name) ?></td>
        </tr>
        <tr>
            <td><strong>Permanent Address:</strong></td>
            <td><?= Html::encode($model->permanent_address) ?></td>
        </tr>
        <tr>
            <td><strong>Telephone Number:</strong></td>
            <td><?= Html::encode($model->telephone) ?></td>
        </tr>
        <tr>
            <td><strong>National ID Number:</strong></td>
            <td><?= Html::encode($model->nic_number) ?></td>
        </tr>
        <tr>
            <td><strong>Business Registration Number:</strong></td>
            <td><?= Html::encode($model->business_reg_number) ?></td>
        </tr>
    </table>

    <h4>Store Place & Transport Details</h4>
    <table class="table table-bordered">
        <tr>
            <td style="width: 50%;"><strong>Species / Type:</strong></td>
            <td style="width: 50%;"><?= !empty($model->species_type) ? Html::encode(implode(', ', (array)$model->species_type)) : "Not provided" ?></td>
        </tr>
        <tr>
            <td><strong>Weight per Purchasing District (Kg):</strong></td>
            <td><?= !empty($model->weight_per_district) ? Html::encode(implode(', ', (array)$model->weight_per_district)) : "Not provided" ?></td>
        </tr>
        <tr>
            <td><strong>Total Weight (Kg):</strong></td>
            <td><?= !empty($model->total_weight) ? Html::encode(implode(', ', (array)$model->total_weight)) : "Not provided" ?></td>
        </tr>
        <tr>
            <td><strong>Purchasing District:</strong></td>
            <td><?= !empty($model->purchasing_district) ? Html::encode(implode(', ', (array)$model->purchasing_district)) : "Not provided" ?></td>
        </tr>
        <tr>
            <td><strong>Intermediate Destination / Store Places:</strong></td>
            <td><?= !empty($model->intermediate_destination) ? Html::encode(implode(', ', (array)$model->intermediate_destination)) : "Not provided" ?></td>
        </tr>
        <tr>
            <td><strong>Final Destination District / Final Store Place:</strong></td>
            <td><?= !empty($model->final_destination) ? Html::encode(implode(', ', (array)$model->final_destination)) : "Not provided" ?></td>
        </tr>
        <tr>
            <td><strong>Vehicle Numbers:</strong></td>
            <td><?= !empty($model->vehicle) ? Html::encode($model->vehicle) : "Not provided" ?></td>
        </tr>
        <tr>
            <td><strong>Boat Numbers:</strong></td>
            <td><?= !empty($model->boat) ? Html::encode($model->boat) : "Not provided" ?></td>
        </tr>
    </table>

    <p>According to your request dated <?= Html::encode($model->request_date) ?>, this Department has no objection of transporting and keep in possession of 
        “NAKLA” (Operculum of Chicoreous ramosus) subjected to the following conditions.</p>
    <ol>
        <li>This letter of no objection if not cancelled early is, valid only for three months’ period from <?= $issueDate->format('Y-m-d') ?> to <?= $expiryDate->format('Y-m-d') ?></li>
        <li>Only <?= !empty($model->total_weight) ? Html::encode(implode(', ', (array)$model->total_weight)) : "Not provided" ?> kgs of “NAKLA” (Operculum of Chicoreous ramosus) from 
        <?= !empty($model->purchasing_district) ? Html::encode(implode(', ', (array)$model->purchasing_district)) : "Not provided" ?> District to <?= !empty($model->intermediate_destination) ? Html::encode(implode(', ', (array)$model->intermediate_destination)) : "Not provided" ?>
        District & to <?= !empty($model->final_destination) ? Html::encode(implode(', ', (array)$model->final_destination)) : "Not provided" ?> District is allowed to keep in position and transport.</li>
        <li>Transport should be confined to the vehicles bearing numbers <?= !empty($model->vehicle) ? Html::encode($model->vehicle) : "Not provided" ?> or to the boats bearing 
        numbers <?= !empty($model->boat) ? Html::encode($model->boat) : "Not provided" ?></li>
        <li>The place of exhibit/sale/store should be confined to store at <?= !empty($model->intermediate_destination) ? Html::encode(implode(', ', (array)$model->intermediate_destination)) : "Not provided" ?></li>
        <li>Only <?= !empty($model->total_weight) ? Html::encode(implode(', ', (array)$model->total_weight)) : "Not provided" ?> kg of NAKLA (Final Product) can be stored at <?= !empty($model->final_destination) ? Html::encode(implode(', ', (array)$model->final_destination)) : "Not provided" ?></li>
        <li>You are liable to get filled the transport tracking table printed the other side when transporting each NAKLA consignment.</li>
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