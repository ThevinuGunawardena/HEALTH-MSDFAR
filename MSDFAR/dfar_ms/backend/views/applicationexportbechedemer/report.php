<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Applicationexportbechedemer $model */

$issueDate = new DateTime();
$expiryDate = (clone $issueDate)->modify('+6 months');
$this->title = 'Licence for ' . Html::encode($model->full_name);
$this->params['breadcrumbs'][] = ['label' => 'Applicationexportbechedemer', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="applicationexportbechedemer-report" id="report-content" style="border: 2px solid #000; padding: 20px; margin: 20px; background-color: #f9f9f9;">

    <h1 style="text-align: center;">DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES</h1>
    <br>
    <p style="text-align: center;">
        P.O.Box 531, New Secretariat Maligawatta, Colombo 10, Sri Lanka<br>
        Telephone: 0094-11-2449170, 2422980, 2434075<br>
        Fax: 0094-11-2449170, 2422980, 2434075<br>
        Email: dgfar@gmail.com
    </p>

    <h3 style="text-align: center;">Licence for Export of Bache-de-mer</h3>
    <br>

    <p><?= Html::encode($model->full_name) ?> of <?= Html::encode($model->address) ?> is hereby authorized to export the
        following consignment. This License if not cancelled previously, 
        shall be valid from Issue Date:<?= $issueDate->format('Y-m-d') ?> to <?= $expiryDate->format('Y-m-d') ?></p>

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
            <td><strong>Business Registration Number:</strong></td>
            <td><?= Html::encode($model->business_reg_number) ?></td>
        </tr>
    </table>

    <h4>Bache-de-mer Export Details</h4>
    <table class="table table-bordered" style="width: 100%;">
        <tr>
            <td style="width: 50%;"><strong>Commercial Name:</strong></td>
            <td style="width: 50%;"><?= Html::encode($model->commercial_name) ?></td>
        </tr>
        <tr>
            <td><strong>Quantity per unit:</strong></td>
            <td><?= Html::encode($model->quantity_value) ?></td>
        </tr>
        <tr>
            <td><strong>Total Weight in Kilograms:</strong></td>
            <td><?= Html::encode($model->total_weight) ?></td>
        </tr>
        <tr>
            <td><strong>Total Number:</strong></td>
            <td><?= Html::encode($model->total_number) ?></td>
        </tr>
        <tr>
            <td><strong>Collection Area:</strong></td>
            <td><?= Html::encode($model->collection_area) ?></td>
        </tr>
        <tr>
            <td><strong>Charges in Rupees:</strong></td>
            <td><?= Html::encode($model->charges ?? "") ?></td>
        </tr>
        <tr>
            <td><strong>Export Countries:</strong></td>
            <td><?= Html::encode($model->export_countries) ?></td>
        </tr>
    </table>

    <p>This license shall be subjected to the following terms and conditions.</p>
    <ol>
        <li>It shall be used by the person named hereto and shall not be transferable to any other persons.</li>
        <li>It shall be subjected to the provisions of Fishing (Import and Export) Regulations, 2010.</li>
        <li>Each kilogram should not contain more than 350 of dried “Pawakka” per kg.</li>
        <li>You are liable to provide particulars of exports done using this permit.</li>
        <li>A new permit will be issued within the valid period with the provision of export data proving that the given quantity has been exported.</li>
        <li>The proprietor/Manager or the Director Board of the company is responsible for any violation of Fishing (Import and Export) Regulations, 2010.</li>
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
