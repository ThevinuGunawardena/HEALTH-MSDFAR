<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Applicationtransportlivefish $model */

$issueDate = new DateTime();
$expiryDate = (clone $issueDate)->modify('+3 months');
$this->title = 'Licence for ' . Html::encode($model->full_name);
$this->params['breadcrumbs'][] = ['label' => 'Applicationtransportlivefish', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="applicationtransportlivefish-report" id="report-content" style="border: 2px solid #000; padding: 20px; margin: 20px; background-color: #f9f9f9;">

    <h1 style="text-align: center;">DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES</h1>
    <br>
    <p style="text-align: center;">
        P.O.Box 531, New Secretariat Maligawatta, Colombo 10, Sri Lanka<br>
        Telephone: 0094-11-2449170, 2422980, 2434075<br>
        Fax: 0094-11-2449170, 2422980, 2434075<br>
        Email: dgfar@gmail.com
    </p>

    <h3 style="text-align: center;">Licence for Transport and keep in possession of Live Fish</h3>
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
            <td><strong>National ID Number:</strong></td>
            <td><?= Html::encode($model->nic_number) ?></td>
        </tr>
        <tr>
            <td><strong>Business Registration Number:</strong></td>
            <td><?= Html::encode($model->business_reg_number) ?></td>
        </tr>
    </table>


    <h4><h4>Store Place & Transport Details</h4></h4>
    <?php if (!empty($storePlaces)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Species</th>
                    <th>Weight per District</th>
                    <th>Purchasing District</th>
                    <th>Intermediate Destination</th>
                    <th>Final Store Place</th>
                    <th>Transport Method</th>
                    <th>Vehicle Number</th>
                    <th>Boat Number</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($storePlaces as $place): ?>
                    <tr>
                        <td><?= Html::encode($place->species) ?></td>
                        <td><?= Html::encode($place->weight_per_distict) ?></td>
                        <td><?= Html::encode($place->purchasing_district) ?></td>
                        <td><?= Html::encode($place->intermediat_destination) ?></td>
                        <td><?= Html::encode($place->final_store_place) ?></td>
                        <td><?= Html::encode($place->transport_method) ?></td>
                        <td><?= Html::encode($place->vehicle_number) ?></td>
                        <td><?= Html::encode($place->boat_number) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No records available.</p>
    <?php endif; ?>

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