<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Applicationtransportbechedemer $model */

$issueDate = new DateTime();
$expiryDate = (clone $issueDate)->modify('+6 months');
$this->title = 'Licence for ' . Html::encode($model->full_name);
$this->params['breadcrumbs'][] = ['label' => 'Applicationtransportbechedemer', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="applicationtransportbechedemer-report" id="report-content" style="border: 2px solid #000; padding: 20px; margin: 20px; background-color: #f9f9f9;">

    <h1 style="text-align: center;">DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES</h1>
    <br>
    <p style="text-align: center;">
        P.O.Box 531, New Secretariat Maligawatta, Colombo 10, Sri Lanka<br>
        Telephone: 0094-11-2449170, 2422980, 2434075<br>
        Fax: 0094-11-2449170, 2422980, 2434075<br>
        Email: dgfar@gmail.com
    </p>

    <h3 style="text-align: center;">Licence for Possession, Exhibit for Sale, Selling or Transport Beche-de-mer</h3>
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
        <!-- <tr>
            <td><strong>Telephone Number:</strong></td>
            <td><?= Html::encode($model->telephone) ?></td>
        </tr> -->
        <tr>
            <td><strong>National ID Number:</strong></td>
            <td><?= Html::encode($model->nic_number) ?></td>
        </tr>
        <tr>
            <td><strong>Business Registration Number:</strong></td>
            <td><?= Html::encode($model->business_reg_number) ?></td>
        </tr>
    </table>

    <p>Above Licence holder is hereby authorised to Possession, Exhibit for Sale, Selling or Transport of Beche-de-mer within 
    <?= !empty($model->purchasing_district) ? Html::encode(implode(', ', (array)$model->purchasing_district)) : "Not provided" ?> and from 
    <?= !empty($model->purchasing_district) ? Html::encode(implode(', ', (array)$model->purchasing_district)) : "Not provided" ?> to
    <?= !empty($model->final_destination) ? Html::encode(implode(', ', (array)$model->final_destination)) : "Not provided" ?> 
    complying with following terms & conditions.</p>

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
            <td><strong>Quantity in pieces:</strong></td>
            <td><?= !empty($model->quantity_pieces) ? Html::encode(implode(', ', (array)$model->quantity_pieces)) : "Not provided" ?></td>
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

    <p>This Licence if not cancelled previously shall be valid for a period of 06 months from <strong>Issue Date:</strong> <?= $issueDate->format('Y-m-d') ?>
    to <strong>Expiry Date:</strong> <?= $expiryDate->format('Y-m-d') ?></p>
    <p>This Permit / Licence shall be subject to the following conditions:</p>
    <ol>
        <li><strong>This shall be used to transport only <?= !empty($model->total_weight) ? Html::encode(implode(', ', (array)$model->total_weight)) : "Not provided" ?> KG
         of Beche-de-mer within <?= !empty($model->purchasing_district) ? Html::encode(implode(', ', (array)$model->purchasing_district)) : "Not provided" ?> & from <?= !empty($model->purchasing_district) ? Html::encode(implode(', ', (array)$model->purchasing_district)) : "Not provided" ?>
         to <?= !empty($model->final_destination) ? Html::encode(implode(', ', (array)$model->final_destination)) : "Not provided" ?> within the above validity period.</strong></li>
        <li>It shall be used by the person named hereto and shall not be transferable to any other persons.</li>
        <li>It shall be subjected to the provisions of Fishing (Import and Export) Regulations, 2010.</li>
        <li>Each kilogram should not contain more than <?= !empty($model->quantity_pieces) ? Html::encode(implode(', ', (array)$model->quantity_pieces)) : "Not provided" ?> pieces of dried Beche-de-mer per kg.</li>
        <li>Transportation within <?= !empty($model->purchasing_district) ? Html::encode(implode(', ', (array)$model->purchasing_district)) : "Not provided" ?>
         District should be confined to the vehicle bearing numbers <?= !empty($model->vehicle) ? Html::encode($model->vehicle) : "Not provided" ?> or to the boat bearing numbers <?= !empty($model->boat) ? Html::encode($model->boat) : "Not provided" ?></li>
        <li><strong>Total quantity of <?= !empty($model->total_weight) ? Html::encode(implode(', ', (array)$model->total_weight)) : "Not provided" ?>
         of dried Beche-de-mer (final product)</strong> can be stored at <?= !empty($model->intermediate_destination) ? Html::encode(implode(', ', (array)$model->intermediate_destination)) : "Not provided" ?></li>
        <li><strong>The transport tracking table printed on the other side of this permit should be filled up when each time for transporting the semi processed or dried Beche-de-mer.</strong></li>
        <li>The Proprietor/ Manager or the Director Board of the company is responsible for any violation of Fishing (Import and Export) Regulations, 2010.</li>
    </ol>

    <h4>Issue Date</h4>
    <p><strong>Issue Date:</strong> <?= date('Y-m-d') ?></p>

    <p style="text-align: right;">
        <strong>E-signature:</strong><br>
        <strong>Director General <br>
        Department of Fisheries & Aquatic Resources</strong>
    </p>

    <!-- <?= Html::a('Download Report', ['download-report', 'id' => $model->id], ['class' => 'btn btn-primary']) ?> -->

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


<!-- <div style="text-align: left; margin: 20px;">
    <?= Html::button('Download Licence', ['class' => 'btn btn-primary', 'onclick' => 'downloadReportAsImage()']) ?>
</div>


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
</script> -->