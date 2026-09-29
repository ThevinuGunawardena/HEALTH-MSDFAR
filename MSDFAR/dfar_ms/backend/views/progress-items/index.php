<?php

use backend\controllers\ProgressItemsController;
use backend\models\MFiDistrict;
use backend\models\MDivision;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */

$this->title = Yii::t('app', 'System Progress Details for Current Year');

$divisionUrl = Url::to(['/division/list-by-district']);

/* Selected filter values */
$fromDate = Yii::$app->request->get('from_date');
$toDate = Yii::$app->request->get('to_date');

$selectedDistrict = Yii::$app->request->get('district_id');
$selectedDivision = Yii::$app->request->get('division_id');

/* District list */
$districtList = ArrayHelper::map(
    MFiDistrict::find()
        ->orderBy(['name' => SORT_ASC])
        ->all(),
    'id',
    'name'
);

/* Load divisions when the page is refreshed after filtering */
$divisionList = [];

if (!empty($selectedDistrict)) {
    $divisionList = ArrayHelper::map(
        MDivision::find()
            ->where([
                'district_id' => $selectedDistrict,
            ])
            ->orderBy([
                'name' => SORT_ASC,
            ])
            ->all(),
        'id',
        'name'
    );
}

/* Get dashboard statistics */
$counts = ProgressItemsController::getStacs(
    $fromDate,
    $toDate,
    $selectedDistrict,
    $selectedDivision
);

$districtStats =
    ProgressItemsController::getFishermanDistrictStats(
        $fromDate,
        $toDate
    );

$boatLicenseDistrictTypeStats =
    ProgressItemsController::getBoatLicenseDistrictBoatTypeStats(
        $fromDate,
        $toDate,
        $selectedDistrict,
        $selectedDivision
    );
?>

<div class="progress-dashboard-header">

    <!-- First row: Dashboard title -->
    <div class="progress-dashboard-title-row">
        <h1 class="progress-dashboard-title">
        </h1>

        <p class="progress-dashboard-subtitle">
            Monitor registrations, renewals and other processes across
            districts and divisions.
        </p>
    </div>

    <!-- Second row: Filters -->
    <div class="progress-dashboard-filter-row">

        <?= Html::beginForm(['index'], 'get', [
            'class' => 'progress-dashboard-filter-form',
            'id' => 'progress-dashboard-filter-form',
        ]) ?>

        <!-- District -->
        <div class="dashboard-select-box">
            <i class="fa fa-map-marker dashboard-filter-icon"></i>

            <?= Html::dropDownList(
                'district_id',
                $selectedDistrict,
                $districtList,
                [
                    'id' => 'district-dropdown',
                    'class' => 'dashboard-select-field',
                    'prompt' => 'All Districts',
                    'aria-label' => 'Select district',
                ]
            ) ?>
        </div>

        <!-- Division -->
        <!-- <div class="dashboard-select-box">
            <i class="fa fa-building dashboard-filter-icon"></i>

            <?= Html::dropDownList(
                'division_id',
                $selectedDivision,
                $divisionList,
                [
                    'id' => 'division-dropdown',
                    'class' => 'dashboard-select-field',
                    'prompt' => 'All Divisions',
                    'aria-label' => 'Select division',
                    'disabled' => empty($selectedDistrict),
                ]
            ) ?>
        </div> -->

        <!-- Date range
        <div class="dashboard-date-wrapper">
            <i class="fa fa-calendar dashboard-filter-icon"></i>

            <?= Html::input(
                'date',
                'from_date',
                $fromDate,
                [
                    'class' => 'dashboard-date-field',
                    'aria-label' => 'From date',
                    'title' => 'From date',
                ]
            ) ?>

            <span class="dashboard-date-separator">–</span>

            <?= Html::input(
                'date',
                'to_date',
                $toDate,
                [
                    'class' => 'dashboard-date-field',
                    'aria-label' => 'To date',
                    'title' => 'To date',
                ]
            ) ?>
        </div> -->

        <?= Html::submitButton(
            '<i class="fa fa-filter"></i><span>Filter</span>',
            [
                'class' => 'btn dashboard-filter-button',
            ]
        ) ?>

        <?= Html::a(
            '<i class="fa fa-refresh"></i><span>Reset</span>',
            ['index'],
            [
                'class' => 'btn dashboard-reset-button',
            ]
        ) ?>

        <?= Html::endForm() ?>

    </div>
</div>

<?= $this->render('../common/reportDashboard', [
    'counts' => $counts,
    'districtStats' => $districtStats,
    'boatLicenseDistrictTypeStats' =>
        $boatLicenseDistrictTypeStats,
]) ?>
<?php

$js = <<<JS
function loadProgressDivisions(districtId, selectedValue) {
    selectedValue = selectedValue || '';

    var divisionDropdown = $('#division-dropdown');

    divisionDropdown
        .empty()
        .append(
            $('<option>', {
                value: '',
                text: 'Loading divisions...'
            })
        )
        .prop('disabled', true);

    if (!districtId) {
        divisionDropdown
            .empty()
            .append(
                $('<option>', {
                    value: '',
                    text: 'All Divisions'
                })
            )
            .prop('disabled', true);

        return;
    }

    $.ajax({
        url: '{$divisionUrl}',
        type: 'GET',
        data: {
            districtId: districtId
        },
        success: function (data) {
            if (typeof data === 'string') {
                try {
                    data = JSON.parse(data);
                } catch (error) {
                    console.error('Invalid division response:', data);

                    divisionDropdown
                        .empty()
                        .append(
                            $('<option>', {
                                value: '',
                                text: 'Invalid division response'
                            })
                        );

                    return;
                }
            }

            divisionDropdown
                .empty()
                .append(
                    $('<option>', {
                        value: '',
                        text: 'All Divisions'
                    })
                );

            $.each(data, function (key, division) {
                divisionDropdown.append(
                    $('<option>', {
                        value: division.id,
                        text: division.name
                    })
                );
            });

            divisionDropdown.prop('disabled', false);

            if (selectedValue !== '') {
                divisionDropdown.val(selectedValue);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error('Division loading error:', errorThrown);
            console.error(jqXHR.responseText);

            divisionDropdown
                .empty()
                .append(
                    $('<option>', {
                        value: '',
                        text: 'Unable to load divisions'
                    })
                )
                .prop('disabled', true);
        }
    });
}

$(document).on('change', '#district-dropdown', function () {
    var districtId = $(this).val();

    loadProgressDivisions(districtId, '');
});
JS;

$this->registerJs($js);
?>