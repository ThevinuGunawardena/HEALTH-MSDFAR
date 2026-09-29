<?php

use backend\models\ProgressItems;
use backend\models\FishermanRegisterdBoatLicense;
use backend\models\Skipper;
use backend\models\ProfileFisherman;
use backend\models\BoatNumbers;
use backend\models\HighseasLicense;
use backend\models\NationalLicense;
use backend\models\ScientificEnumerationRequest;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\db\Expression;

/** @var yii\web\View $this */
/** @var backend\models\ProgressItemsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Report');
$this->params['breadcrumbs'][] = $this->title;

// Calculate the sum of records for each model
$webURL = Yii::getAlias('@web');

?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <!-- Button to trigger PDF download -->
            <a href="progress-items-download" class="btn btn-success">Download PDF</a>
        </div>
    </div>
</div>
<div class="col-xl-12"><br>


<center><h3>SOFTWARE SOLUTION <br>
FOR OPERATIONAL AND ADMINISTRATION  <br>
ACTIVITIES OF DFAR <br></center>
</h3>
<h4>1.0	Introduction </h4>
<p>
Department of Fisheries and Aquatic Resources (DFAR) under the purview of the Ministry of Fisheries and Aquatic Resources Development is the department of the central government responsible for management, regulation, conservation and development of fisheries and aquatic resources. It was established in 1940 under the provisions of the Fisheries Ordinance (Law No. 24 of 1940), which has now been repealed. DFAR enforces provisions of the Fisheries and Aquatic Resources Act, No. 2 of 1996 and Fisheries (Regulation of Foreign Fishing Boats) Act, No. 59 of 1979. DFAR also undertakes monitoring and surveillance activities in the sea and helps the fishers in distress. 
<br><br>
With latest development in the sector and the requirements of national and international fisheries management, the responsibilities of the department were widened. Also, the department was named as the focal point of international fisheries management activities and compliances. Recently it was highlighted that department should take actions increase the fisheries contribution to the national economy by focusing on the activities related to the fisheries technology and fisheries finance. The scope of the department is highly widen over the years due to the fact that number of boats and the number of fishermen to be deal with is increased from 1970s to the present over 300% and 225% respectively.
 <br><br>
Therefore, there was a need to provide a solution to manage the complex procedures related to high number of boats and fishermen and to deal with the limited manpower available at the department. It was decided to adopt Information and Communication Technology (ICT) to improve the efficiency and effectiveness of the processes. Accordingly, it was proposed to establish a comprehensive, centralized, and web/ server-based solution which can cater to all the requirements with the ability of information sharing and support for decision/policy making.
<h4>2.0 Software development process </h4>

<p>
Accordingly, a software to handle management and administrative process (called MSDFAR) was introduce in 2021 this software was initially developed by the Epic Technologies Pvt. Ltd.  However due to the needs on the improvements and to increase the efficiency of the software and the improved second version of the same was introduced in 2024. Here latest requirements to expand the software to the coastal fisheries sector fisheries management and the technical advancement towards cyber security was also considered for the development of the second version of MSDFAR. Here the financial and technical support of Pelagikos Pvt Ltd was granted for the development process of the same. Development was done by the IT division of DFAR under the guidance of software consulting firm hired by pelagikos pvt. Ltd. 
<br><br>
Following important process and functions related to the duties of DFAR was covered via the version 02 of MSDFSAR. Here for some of the processes, Version 01 only cater for multiday boats and the version 02 also facilitate the all the other types of boats including costal fishing boats
<br><br>
</p>

<ol>
    <li>Boat registration, renewal of registration, transfer of registration and cancelling of registration (expanded to coastal fisheries) </li>
    <li>Issuing registration numbers for new boats (expanded to coastal fisheries)</li>
    <li>Issuing stability and feasibility reports for boats (improved)</li>
    <li>Operational licenses for fisheries operations (improved and expanded to coastal fisheries)</li>
    <li>Issuing of fisheries identity cards (improved)</li>
    <li>Issuing of skipper licenses (improved)</li>
    <li>Scientific data collection, analysis and reporting (improved and expanded to coastal fisheries)</li>
    <li>Boat designs/ Boat Yards (improved and expanded to coastal fisheries)</li>
    <li>Dashboard and reports (improved and some aspects introduced)</li>
    <li>Alerts and notifications (improved)</li>
</ol>
<br><br>

<p>
Aspects such as departure and arrival procedure, special fisheries management license issued by the head office of DFAR and catch/ health certificate for fish exports was not covered via the version 02. 
</p>

<br><br>
<h4>3.0 Highlights of MSDFAR version 02</h4>

<p>
Here the fishermen registration process was improved with a new concept by introducing online fishermen profiles so that all fishermen can have a online login to edit their basic data and to track the status of their license. Also, the fisheries ID printing app was converted to a web-based user friendly one. Bugs identified in almost all the process of version 01 was fixed in the version 02. Temporary skipper IDs and fishermen IDs were introduced to overcome the delays of issuing the permanent IDs. Scientific app was improved immensely to make this complicated app to a user friendly one. All manual data available at Management division of DFAR were migrated to the version 02 to fill the gaps of data. The fisheries management areas concept was introduced to the system to provide the requirement to support the upcoming regulation on the same. 
</p>
<p>
Fishermen were allowed to apply their licenses with one click approach. Here the details relevant to the application were generated via the software and data feeding is minimum at the point of applying. Here the Fisheries inspectors were tasked to define the fishing gear types of their respective area, so that types of fishing gears with specifications are available at a dropdown menu when fishermen were asked to select the relevant fishing gear of their license. Over 500 such fishing gear types are now available in the system. 
</p>

<p>
Moreover, the second version build using PHP and MySQL technologies with a software structure to provide IT division to an opportunity to incorporate the future adding and improvement much more convenient way. 
</p>

</p>
Implementation of version 02 was initiated from 01st of January 2024 and the progress off the activities carried out are given below. 
</p>
<br><br>
<h4>Improvement Data</h4>

<div class="col-xl-12">
<table class="table">
    <thead>
        <tr>
            <th scope="col">Activity</th>
            <th scope="col">To <?= $monthBeforePreviousName ?></th>
            <th scope="col">In <?= $previousMonthName ?></th>
            <th scope="col">Incrrease by Persentage</th>

            <th scope="col">Up to <?= $previousMonthName ?></th>
        </tr>
    </thead>
    <tbody>
       
        <tr>
            <th scope="row">Registered Boat License</th>
            <td><?= $boatRegistrationcountuptoMonthBeforePrevious ?></td>
            <td><?= $boatRegistrationcountinPreviousMonth ?></td>
            <td><?= $boatRegistrationpersentage . '%'?></td>
            <td><?= $boatRegistrationcountuptoPreviousMonth ?></td>

        </tr>
        <tr>
            <th scope="row">Registered Boat License - Renewal</th>
            <td><?= $renewboatRegistrationcountuptoMonthBeforePrevious ?></td>
            <td><?= $renewboatRegistrationcountinPreviousMonth ?></td>
            <td><?= $renewboatRegistrationpersentage . '%'?></td>
            <td><?= $renewboatRegistrationcountuptoPreviousMonth ?></td>

        </tr>
        <tr>
            <th scope="row">Registered Boat License - IMUL</th>
            <td><?= $boatRegistrationcountIMULuptoMonthBeforePrevious ?></td>
            <td><?= $boatRegistrationIMULcountinPreviousMonth ?></td>
            <td><?= $boatRegistrationIMULPercentage . '%'?></td>
            <td><?= $boatRegistrationIMULcountuptoPreviousMonth ?></td>

        </tr>

        <tr>
            <th scope="row">Registered Boat License - IMUL Renewal</th>
            <td><?= $renewboatRegistrationcountIMULuptoMonthBeforePrevious ?></td>
            <td><?= $renewboatRegistrationIMULcountinPreviousMonth ?></td>
            <td><?= $renewboatRegistrationIMULPercentage . '%'?></td>
            <td><?= $renewboatRegistrationIMULcountuptoPreviousMonth ?></td>

        </tr>

        <tr>
            <th scope="row">Registered Boat License - Other</th>
            <td><?= $boatRegistrationcountOtheruptoMonthBeforePrevious ?></td>
            <td><?= $boatRegistrationOthercountinPreviousMonth ?></td>
            <td><?= $boatRegistrationOtherPercentage . '%'?></td>
            <td><?= $boatRegistrationOthercountuptoPreviousMonth ?></td>

        </tr>
        <tr>
            <th scope="row">Registered Boat License - Other Renewal</th>
            <td><?= $renewboatRegistrationcountOtheruptoMonthBeforePrevious ?></td>
            <td><?= $renewboatRegistrationOthercountinPreviousMonth ?></td>
            <td><?= $renewboatRegistrationOtherPercentage . '%'?></td>
            <td><?= $renewboatRegistrationOthercountuptoPreviousMonth ?></td>

        </tr>
        <tr>
            <th scope="row">Skipper License</th>
            <td><?= $SkipperuptoMonthBeforePrevious ?></td>
            <td><?= $SkippercountinPreviousMonth ?></td>
            <td><?= $SkipperPercentage . '%'?></td>
            <td><?= $SkippercountuptoPreviousMonth ?></td>

        </tr>
        <tr>
            <th scope="row">Skipper License - Renewals</th>
            <td><?= $renewSkipperuptoMonthBeforePrevious ?></td>
            <td><?= $renewSkippercountinPreviousMonth ?></td>
            <td><?= $renewSkipperPercentage . '%'?></td>
            <td><?= $renewSkippercountuptoPreviousMonth ?></td>

        </tr>
        <tr>
            <th scope="row">Fisherman Registration</th>
            <td><?= $FishermanuptoMonthBeforePrevious ?></td>
            <td><?= $FishermancountinPreviousMonth ?></td>
            <td><?= $FishermanPercentage . '%'?></td>
            <td><?= $FishermancountuptoPreviousMonth ?></td>

        </tr>

        <tr>
            <th scope="row">Boat Number Issuing - IMUL</th>
            <td><?= $BoatnumbernuptoMonthBeforePreviousIMUL ?></td>
            <td><?= $BoatnumbercountinPreviousMonthIMUL ?></td>
            <td><?= $BoatnumberPercentageIMUL . '%'?></td>
            <td><?= $BoatnumbercountuptoPreviousMonthIMUL ?></td>

        </tr>

        <tr>
            <th scope="row">Boat Number Issuing - Other</th>
            <td><?= $BoatnumbernuptoMonthBeforePreviousOther ?></td>
            <td><?= $BoatnumbercountinPreviousMonthOther ?></td>
            <td><?= $BoatnumberPercentageOther . '%'?></td>
            <td><?= $BoatnumbercountuptoPreviousMonthOther ?></td>

        </tr>

        <tr>
            <th scope="row">Highseas License Issuing</th>
            <td><?= $HighseasLicenseuptoMonthBeforePrevious ?></td>
            <td><?= $HighseasLicensecountinPreviousMonth ?></td>
            <td><?= $HighseasLicensePercentage . '%'?></td>
            <td><?= $HighseasLicensecountuptoPreviousMonth ?></td>

        </tr>

        <tr>
            <th scope="row">Highseas License Issuing - Renewals</th>
            <td><?= $renewHighseasLicenseuptoMonthBeforePrevious ?></td>
            <td><?= $renewHighseasLicensecountinPreviousMonth ?></td>
            <td><?= $renewHighseasLicensePercentage . '%'?></td>
            <td><?= $renewHighseasLicensecountuptoPreviousMonth ?></td>

        </tr>

        <tr>
            <th scope="row">National License Issuing (IMUL)</th>
            <td><?= $NationalLicenseIMULuptoMonthBeforePrevious ?></td>
            <td><?= $NationalLicenseIMULcountinPreviousMonth ?></td>
            <td><?= $NationalLicenseIMULPercentage . '%'?></td>
            <td><?= $NationalLicenseIMULcountuptoPreviousMonth ?></td>

        </tr>
        <tr>
            <th scope="row">National License Issuing (IMUL) - Renewals</th>
            <td><?= $renewNationalLicenseIMULuptoMonthBeforePrevious ?></td>
            <td><?= $renewNationalLicenseIMULcountinPreviousMonth ?></td>
            <td><?= $renewNationalLicenseIMULPercentage . '%'?></td>
            <td><?= $renewNationalLicenseIMULcountuptoPreviousMonth ?></td>

        </tr>
        <tr>
            <th scope="row">National License Issuing (Other)</th>
            <td><?= $NationalLicenseOtheruptoMonthBeforePrevious ?></td>
            <td><?= $NationalLicenseOthercountinPreviousMonth ?></td>
            <td><?= $NationalLicenseOtherPercentage . '%'?></td>
            <td><?= $NationalLicenseOthercountuptoPreviousMonth ?></td>

        </tr>

        <tr>
            <th scope="row">National License Issuing (Other) - Renewals</th>
            <td><?= $renewNationalLicenseOtheruptoMonthBeforePrevious ?></td>
            <td><?= $renewNationalLicenseOthercountinPreviousMonth ?></td>
            <td><?= $renewNationalLicenseOtherPercentage . '%'?></td>
            <td><?= $renewNationalLicenseOthercountuptoPreviousMonth ?></td>

        </tr>


        
    </tbody>
</table>


<br></br>
<h2>Previous System Data</h2>

<table class="table">
    <thead>
        <tr>
            <th scope="col">Activity</th>
            <th scope="col">Count</th>
        </tr>
    </thead>
    <tbody>
       
        <tr>
            <th scope="row">Registered Boat License</th>
            <td><?= $boatRegistrationcountPrevious ?></td>
        </tr>

        <tr>
            <th scope="row">Registered Boat License - Multiday Boats</th>
            <td><?= $IMULboatRegistrationcountPrevious ?></td>
        </tr>


        <tr>
            <th scope="row">Registered Boat License - Costal Boats</th>
            <td><?= $OtherboatRegistrationcountPrevious ?></td>
        </tr>


        <tr>
        <th scope="row">Skipper License	</th>
        <td><?= $skipperPrevious ?></td>
        </tr>

        <tr>
        <th scope="row">Fisherman Registration</th>
        <td><?= $fishermanPrevious ?></td>
        </tr>

        <tr>
        <th scope="row">Boat Number Issuing - Multiday</th>
        <td><?= $boatNumberPreviousIMUL ?></td>
        </tr>

        <tr>
        <th scope="row">Boat Number Issuing - Costal</th>
        <td><?= $boatNumberPreviousOther ?></td>
        </tr>

        <tr>
        <th scope="row">National License Issuing</th>
        <td><?= $NatinalLicnesePrevious ?></td>
        </tr>

        <tr>
        <th scope="row">Highseas License Issuing</th>
        <td><?= $HighseasPrevious ?></td>
        </tr>
</tbody>
</table>
</div>
<canvas id="boatOwnersChart" width="800" height="400"></canvas>
<canvas id="monthlyDataChart" width="800" height="400"></canvas>

<h4>Number Per Month</h4>

<table id="monthlyDataTable" border="1" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
        <tr>
            <th>Activity</th>
            <!-- This header will be filled dynamically -->
        </tr>
    </thead>
    <tbody>
       
    </tbody>
</table>


<?php
// Fetch counts for all months in 2024
$months = range(1, 12); // 1 to 12 represents Jan-Dec

// Function to get cumulative counts for all months of a specific year
function getCumulativeCountsForYear($model, $year, $months) {
    $monthlyData = [];

    foreach ($months as $month) {
        $monthlyData[$month] = $model::find()
            ->select([
                'month' => new Expression('MONTH(created)'),
                'count' => new Expression('COUNT(*)')
            ])
            ->where(['YEAR(created)' => $year])
            ->andWhere(['MONTH(created)' => $month])
            ->groupBy(new Expression('MONTH(created)'))
            ->asArray()
            ->one();  // Use one() to get data for each month

        // If no data for the month, set count to 0
        if (!$monthlyData[$month]) {
            $monthlyData[$month]['count'] = 0;
        }
    }
    
    return $monthlyData;
}
$currentMonth = (new \DateTime())->format('m'); // Current month (01 to 12)
$currentYear = (new \DateTime())->format('Y'); // Current year (e.g., 2025)

// If it's January, set the year to the previous year
if ($currentMonth == '01') {
    $yearToUse = $currentYear - 1; // Previous year (e.g., 2024)
} else {
    $yearToUse = $currentYear; // Current year (e.g., 2025)
}

// Get cumulative counts for each dataset (fisherman, boat numbers, licenses, etc.)
$fishermanRegistrationData = getCumulativeCountsForYear(ProfileFisherman::class, $yearToUse, $months);
$boatNumbersIMULData = getCumulativeCountsForYear(BoatNumbers::class, $yearToUse, $months);
$boatNumbersOtherData = getCumulativeCountsForYear(BoatNumbers::class, $yearToUse, $months);
$nationalLicenseCoastalData = getCumulativeCountsForYear(NationalLicense::class, $yearToUse, $months);
$nationalLicenseIMULData = getCumulativeCountsForYear(NationalLicense::class, $yearToUse, $months);
$skipperData = getCumulativeCountsForYear(Skipper::class, $yearToUse, $months);
$boatRegistrationIMULData = getCumulativeCountsForYear(FishermanRegisterdBoatLicense::class, $yearToUse, $months);
$boatRegistrationOtherData = getCumulativeCountsForYear(FishermanRegisterdBoatLicense::class, $yearToUse, $months);
$highseasLicenseData = getCumulativeCountsForYear(HighseasLicense::class, $yearToUse, $months);
?>

<ul>
    <li>
        Data of over 60000 fishermen and over 25000 boats were migrated to the version 02 of MSDFAR to allowing the smooth transition from version 01. Data of the previous owners of more than 5000 multiday boats were added to the system. 
    </li>
    <li>
        Here over <?= $OfficerProfilesUptoDateCount ?> officers were provided with logins in the version 02 while over <?= $FishermanProfilesUptoDateCount ?> fishermen profiles were created UpToDate. 
    </li>
    <li>
        All parts of the version 02 is developped and in practice. bugs identified in the boat registration, licensing and scientific data applications are also fixed 
    </li>
</ul>

<br></br>
<h4>3.0 Efficiency of the system </h4>
<p>
The average time taken to complete the process that were subjected to the digitization was evaluated and the results are given below.
<style>
table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
       

</style>
<table>
    <tr>
        <th>Type of Duty</th>
        <th>Time to Complete via Manual System</th>
        <th>Time to Complete via Software System</th>
    </tr>
    <tr>
        <td>Fishermen Registration</td>
        <td>15 Days</td>
        <td>30 Minutes</td>
    </tr>
    <tr>
        <td>Fishermen ID card issuing </td>
        <td>45 Days </td>
        <td>10-5 days </td>
    </tr>
    <tr>
        <td>Skipper licence issuing </td>
        <td>90 Days</td>
        <td>5 days</td>
    </tr>
    <tr>
        <td>Temporary ID cars and skipper licenses issuing </td>
        <td>Not available in manual system </td>
        <td>One day</td>
    </tr>
    <tr>
        <td>Boat registration/ renewal </td>
        <td>30 days </td>
        <td>3 days </td>
    </tr>
    <tr>
        <td>Issuing boat registration numbers </td>
        <td>45 days </td>
        <td>14 Days</td>
    </tr>
    <tr>
        <td>High seas Licenses issuing  </td>
        <td>10  days </td>
        <td>One day</td>
    </tr>
    <tr>
        <td>EEZ and costal operation licenses   </td>
        <td>07  days </td>
        <td>One day</td>
    </tr>
    <tr>
        <td>Boat suitability report (stability)  </td>
        <td>14 days </td>
        <td>02 Days</td>
    </tr>
    <tr>
        <td>Scientific inspections and data reporting  </td>
        <td>30 days </td>
        <td>One day</td>
    </tr>
    

</table>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>

<script>
const districtNames = <?php echo json_encode(array_values($districtNames)); ?>;
    const withLoginCounts = <?php echo json_encode(array_values($districtWithLoginCounts)); ?>;
    const withoutLoginCounts = <?php echo json_encode(array_values($districtWithoutLoginCounts)); ?>;

    // Adjust null values to be represented correctly in the chart
    const adjustedWithLoginCounts = withLoginCounts.map(value => value === null ? 0 : value);
    const adjustedWithoutLoginCounts = withoutLoginCounts.map(value => value === null ? 0 : value);

    const ctx = document.getElementById('boatOwnersChart').getContext('2d');
    const boatOwnersChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: districtNames,
            datasets: [
                {
                    label: 'Fishermans with Login',
                    data: adjustedWithLoginCounts,
                    backgroundColor: 'rgba(0, 128, 0, 1)',
                    borderColor: 'rgba(0, 128, 0, 0.4)',
                    borderWidth: 1
                },
                {
                    label: 'Target of Login Creation',
                    data: adjustedWithoutLoginCounts,
                    backgroundColor: 'rgba(128, 0, 0, 1)',
                    borderColor: 'rgba(128, 0, 0, 0.4)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Boat Owners'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Districts'
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.raw === 0 ? "No Data" : context.raw; // Display "No Data" if count is 0
                            return label;
                        }
                    }
                }
            }
        }
    });
</script>



<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Your custom script that calls html2canvas
        document.addEventListener("DOMContentLoaded", function () {
            convertCanvasToImages();
        });

        function convertCanvasToImages() {
            const canvasIds = ['FishermanandSkipper', 'monthlyDataChart'];
            let conversionsCompleted = 0;

            canvasIds.forEach(id => {
                const canvasElement = document.getElementById(id);
                if (canvasElement) {
                    html2canvas(canvasElement).then(canvas => {
                        const imgData = canvas.toDataURL("image/png");
                        document.getElementById(id + '_base64').value = imgData;

                        conversionsCompleted++;
                        console.log(`Canvas ${id} converted. Conversions completed: ${conversionsCompleted}`);

                        if (conversionsCompleted === canvasIds.length) {
                            document.getElementById("downloadPdfButton").style.display = "block";
                        }
                    }).catch(error => {
                        console.error(`Error converting canvas ${id}: `, error);
                    });
                } else {
                    console.error(`Canvas element with id ${id} not found.`);
                }
            });
        }
        function triggerPdfDownload() {
            window.location.href = '<?= \yii\helpers\Url::to(['progress-items/summary', 'format' => 'pdf']) ?>';
        }

        document.addEventListener("DOMContentLoaded", function () {
            generateCharts(); // Ensure this function is defined
            convertCanvasToImages(); // Convert canvases to images
        });
    </script>

<script>
// JavaScript data arrays for Chart.js
const months = <?php echo json_encode(array_map(function($m) { return date("F", mktime(0, 0, 0, $m, 1)); }, $months)); ?>;
const fishermanCounts = <?php echo json_encode(array_column($fishermanRegistrationData, 'count')); ?>;
const skipperCounts = <?php echo json_encode(array_column($skipperData, 'count')); ?>;
const boatCountsIMUL = <?php echo json_encode(array_column($boatNumbersIMULData, 'count')); ?>;
const boatCountsOther = <?php echo json_encode(array_column($boatNumbersOtherData, 'count')); ?>;
const boatRegistrationCountsIMUL = <?php echo json_encode(array_column($boatRegistrationIMULData, 'count')); ?>;
const boatRegistrationCountsOther = <?php echo json_encode(array_column($boatRegistrationOtherData, 'count')); ?>;
const coastalLicenseCounts = <?php echo json_encode(array_column($nationalLicenseCoastalData, 'count')); ?>;
const imulLicenseCounts = <?php echo json_encode(array_column($nationalLicenseIMULData, 'count')); ?>;
const highseasLicenseCounts = <?php echo json_encode(array_column($highseasLicenseData, 'count')); ?>;


// Create cumulative chart
const mdc = document.getElementById('monthlyDataChart').getContext('2d');
const monthlyProgressChart = new Chart(mdc, {
    type: 'line',
    data: {
        labels: months,
        datasets: [
            {
                label: 'Fisherman Registrations',
                data: fishermanCounts,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                //fill: true
            },
            {
                label: 'Skipper Registrations',
                data: skipperCounts,
                borderColor: 'rgba(162, 162, 90, 1)',
                backgroundColor: 'rgba(162, 162, 90, 0.2)',
                //fill: true
            },
            {
                label: 'Boat Numbers IMUL',
                data: boatCountsIMUL,
                borderColor: 'rgba(255, 159, 64, 1)',
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                //fill: true
            },
            {
                label: 'Boat Numbers Other',
                data: boatCountsOther,
                borderColor: 'rgba(76, 0, 153, 1)',
                backgroundColor: 'rgba(76, 0, 153, 0.2)',
                //fill: true
            },
            {
                label: 'Boat Registration IMUL',
                data: boatRegistrationCountsIMUL,
                borderColor: 'rgba(153, 0, 153, 1)',
                backgroundColor: 'rgba(153, 0, 153, 0.2)',
                //fill: true
            },
            {
                label: 'Boat Registration Other',
                data: boatRegistrationCountsOther,
                borderColor: 'rgba(153, 0, 10, 1)',
                backgroundColor: 'rgba(153, 0, 10, 0.2)',
                //fill: true
            },
            {
                label: 'Coastal National License Counts',
                data: coastalLicenseCounts,
                borderColor: 'rgba(153, 102, 255, 1)',
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                //fill: true
            },
            {
                label: 'IMUL National License Counts',
                data: imulLicenseCounts,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                //fill: true
            },
            {
                label: 'Highseas License Counts',
                data: highseasLicenseCounts,
                borderColor: 'rgba(156, 76, 0, 1)',
                backgroundColor: 'rgba(156, 76, 0, 0.2)',
                //fill: true
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Month'
                }
            },
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Monthly Count'
                }
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'top',
            }
        }
    }
});


function populateMonthlyDataTable() {
    const monthsHeader = document.querySelector('#monthlyDataTable thead tr');

    // Clear existing header cells to prevent duplication
    while (monthsHeader.children.length > 1) {
        monthsHeader.removeChild(monthsHeader.lastChild);
    }

    // Add months as header cells
    months.forEach(month => {
        const th = document.createElement('th');
        th.textContent = month;
        monthsHeader.appendChild(th);
    });

    // Clear existing table body rows
    const tableBody = document.querySelector('#monthlyDataTable tbody');
    tableBody.innerHTML = ''; // Clear previous content

    // Populate counts for each activity
    const activities = [
        { name: 'Fisherman Registrations', counts: fishermanCounts },
        { name: 'Skipper Registrations', counts: skipperCounts },
        { name: 'Boat Numbers IMUL', counts: boatCountsIMUL },
        { name: 'Boat Numbers Other', counts: boatCountsOther },
        { name: 'Boat Registration IMUL', counts: boatregostrationCountsIMUL },
        { name: 'Boat Registration Other', counts: boatregostrationCountsOther },
        { name: 'Coastal National License Counts', counts: coastalLicenseCounts },
        { name: 'IMUL National License Counts', counts: imulLicenseCounts },
        { name: 'Highseas License Count', counts: HighseasLicenseCounts },
    ];

    activities.forEach(activity => {
        const row = document.createElement('tr');
        const tdActivity = document.createElement('td');
        tdActivity.textContent = activity.name;
        row.appendChild(tdActivity);

        // Append counts for each month
        activity.counts.forEach((count, index) => {
            const tdCount = document.createElement('td');
            tdCount.textContent = count || 0; // Default to 0 if no count available
            row.appendChild(tdCount);
        });

        // Append the row to the table body
        tableBody.appendChild(row);
    });
}

// Call the function to populate the table
populateMonthlyDataTable();

function generateCumulativeDataTable() {
    // Start with the table structure
    let tableHTML = `<table border="1" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <th>Month</th>
                <th>Cumulative Fisherman Registrations</th>
                <th>Cumulative Skipper Registrations</th>
                <th>Cumulative Boat Numbers IMUL</th>
                <th>Cumulative Boat Numbers Other</th>
                <th>Cumulative Boat Registration IMUL</th>
                <th>Cumulative Boat Registration Other</th>
                <th>Cumulative Coastal License Counts</th>
                <th>Cumulative IMUL License Counts</th>
                <th>Cumulative Highseas License Counts</th>
            </tr>
        </thead>
        <tbody>`;

    // Populate each row with data from the arrays
    for (let i = 0; i < months.length; i++) {
        tableHTML += `<tr>
            <td>${months[i]}</td>
            <td>${cumulativeFishermanCounts[i] || 0}</td>
            <td>${cumulativeSkipperCounts[i] || 0}</td>
            <td>${cumulativeBoatCountsIMUL[i] || 0}</td>
            <td>${cumulativeBoatCountsOther[i] || 0}</td>
            <td>${cumulativeBoatRegistrationCountsIMUL[i] || 0}</td>
            <td>${cumulativeBoatRegistrationCountsOther[i] || 0}</td>
            <td>${cumulativeCoastalLicenseCounts[i] || 0}</td>
            <td>${cumulativeImulLicenseCounts[i] || 0}</td>
            <td>${cumulativeHighseasLicenseCounts[i] || 0}</td>
        </tr>`;
    }

    // Close the table structure
    tableHTML += `</tbody></table>`;

    // Insert the table HTML into the container
    document.getElementById('cumulativeDataTableContainer').innerHTML = tableHTML;
}

// Call the function to generate and display the table
generateCumulativeDataTable();
</script>

