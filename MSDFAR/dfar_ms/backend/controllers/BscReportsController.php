<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\Bsc1Submission;
use backend\models\Bsc2Submission;
use backend\models\MFiDistrict;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


/**
 * Reporting controller for Development Division — national, validated-only
 * rollups of BSC-1/BSC-2 data, grouped by district with division-level
 * detail and an island-wide grand total, plus an Excel export of the same
 * data. Distinct from BscFormsController, which handles day-to-day FI/AD
 * data entry and review — this controller only ever reads.
 *
 * Access is gated on Constant::DEVELOPMENT_DIVISION — a genuinely new
 * role, confirmed not to previously exist in this system. Requires:
 *   1. const DEVELOPMENT_DIVISION = 22; added to Constant.php (confirm 22
 *      is actually unused first — search the file for "= 22").
 *   2. An entry in Constant::$userTypes for it (category 'main').
 *   3. Constant::DEVELOPMENT_DIVISION added to the district-required
 *      exemption list in ProfileOfficers::rules() — this role is
 *      island-wide, same as DG/DM/Director, so it shouldn't be forced to
 *      have a district on its profile.
 *
 * ── DEPENDENCY ─────────────────────────────────────────────────────────
 * Uses PhpOffice\PhpSpreadsheet for the Excel export. Confirm
 * "phpoffice/phpspreadsheet" is listed in composer.json before running
 * actionExportExcel() — if it's not installed, that action will fatal
 * with a class-not-found error, same shape as the earlier Bsc1Boat issue.
 */
class BscReportsController extends Controller
{
    /**
     * Print-friendly on-screen national report.
     * @param string $form 'bsc1' or 'bsc2'
     */
    public function actionNational($form = 'bsc1', $period = null)
    {
        $this->requireType(Constant::DEVELOPMENT_DIVISION);

        $periodDate = $this->resolvePeriodDate($period);
        [$byDistrict, $headers, $grandTotals] = $this->buildNationalReportData($form, $periodDate);

        return $this->render('national-report-' . $form, [
            'form' => $form,
            'periodDate' => $periodDate,
            'byDistrict' => $byDistrict,
            'headers' => $headers,
            'grandTotals' => $grandTotals,
        ]);
    }

    /**
     * Same data as actionNational(), as an .xlsx download instead.
     * @param string $form 'bsc1' or 'bsc2'
     */
    public function actionExportExcel($form = 'bsc1', $period = null)
    {
        $this->requireType(Constant::DEVELOPMENT_DIVISION);

        $periodDate = $this->resolvePeriodDate($period);

        /*
        * ============================================================
        * BSC-1 EXCEL EXPORT
        * ============================================================
        */

        if ($form === 'bsc1') {

            $submissions = Bsc1Submission::find()
                ->with([
                    'division',
                    'division.district',
                    'boats',
                    'registrations',
                    'licensesWithCraft',
                    'awarenessProgrammes',
                ])
                ->joinWith('division')
                ->where([
                    'bsc1_submissions.period_date' => $periodDate,
                    'bsc1_submissions.approval_stage' =>
                        (string) Constant::DEVELOPMENT_DIVISION,
                ])
                ->orderBy([
                    'm_division.district_id' => SORT_ASC,
                    'm_division.name' => SORT_ASC,
                ])
                ->all();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('BSC-1 National');


            // ============================================================
            // HEADER ROWS
            // ============================================================

            // District and F.I. Division
            $sheet->mergeCells('A1:A3');
            $sheet->setCellValue('A1', 'District');

            $sheet->mergeCells('B1:B3');
            $sheet->setCellValue('B1', 'F.I. Division');


            // ------------------------------------------------------------
            // Community Profile
            // ------------------------------------------------------------

            $sheet->mergeCells('C1:E2');
            $sheet->setCellValue('C1', 'Community Profile');

            $communityHeaders = [
                'Fisher Families',
                'Active Fishermen',
                'Fisheries Sector Population',
            ];

            foreach ($communityHeaders as $index => $header) {
                $column = Coordinate::stringFromColumnIndex(3 + $index); // C
                $sheet->setCellValue($column . '3', $header);
            }


            // ------------------------------------------------------------
            // Existing Boats
            // ------------------------------------------------------------

            $sheet->mergeCells('F1:L2');
            $sheet->setCellValue('F1', 'Existing Boats');

            $existingBoatHeaders = [
                'IMUL >50',
                'IMUL',
                'IDAY',
                'OFRP',
                'MTRB',
                'NTRB',
                'NBSB',
            ];

            foreach ($existingBoatHeaders as $index => $header) {
                $column = Coordinate::stringFromColumnIndex(6 + $index); // F
                $sheet->setCellValue($column . '3', $header);
            }


            // ------------------------------------------------------------
            // Boat Registrations
            // ------------------------------------------------------------

            $sheet->mergeCells('M1:AG1');
            $sheet->setCellValue('M1', 'Boat Registrations');

            $sheet->mergeCells('M2:S2');
            $sheet->setCellValue('M2', 'First Registrations');

            $sheet->mergeCells('T2:Z2');
            $sheet->setCellValue('T2', 'Renewals');

            $sheet->mergeCells('AA2:AG2');
            $sheet->setCellValue('AA2', 'Cancellations');

            $registrationCrafts = [
                ['label' => 'IMUL >50', 'key' => 'imul_over50'],
                ['label' => 'IMUL',     'key' => 'imul'],
                ['label' => 'IDAY',     'key' => 'iday'],
                ['label' => 'OFRP',     'key' => 'ofrp'],
                ['label' => 'MTRB',     'key' => 'mtrb'],
                ['label' => 'NTRB',     'key' => 'ntrb'],
                ['label' => 'NBSB',     'key' => 'nbsb'],
            ];

            foreach ($registrationCrafts as $index => $craft) {

                $header = $craft['label'];

                // First Registrations: M:S
                $column = Coordinate::stringFromColumnIndex(13 + $index);
                $sheet->setCellValue($column . '3', $header);

                // Renewals: T:Z
                $column = Coordinate::stringFromColumnIndex(20 + $index);
                $sheet->setCellValue($column . '3', $header);

                // Cancellations: AA:AG
                $column = Coordinate::stringFromColumnIndex(27 + $index);
                $sheet->setCellValue($column . '3', $header);
            }

            // ------------------------------------------------------------
            // Licences
            // ------------------------------------------------------------

            $sheet->mergeCells('AH1:AP2');
            $sheet->setCellValue('AH1', 'Licences');

            $licenceHeaders = [
                'Without Craft',
                'IMUL',
                'IDAY',
                'OFRP',
                'MTRB',
                'NTRB',
                'NBSB',
                'Total With Craft',
                'Total Licences',
            ];

            foreach ($licenceHeaders as $index => $header) {
                $column = Coordinate::stringFromColumnIndex(34 + $index); // AH
                $sheet->setCellValue($column . '3', $header);
            }


            // ------------------------------------------------------------
            // Migrated Boats
            // ------------------------------------------------------------

            $sheet->mergeCells('AQ1:AS2');
            $sheet->setCellValue('AQ1', 'Migrated Boats');

            $migratedBoatHeaders = [
                'IMUL',
                'IDAY',
                'OFRP',
            ];

            foreach ($migratedBoatHeaders as $index => $header) {
                $column = Coordinate::stringFromColumnIndex(43 + $index); // AQ
                $sheet->setCellValue($column . '3', $header);
            }


            // ------------------------------------------------------------
            // Incidents
            // ------------------------------------------------------------

            $sheet->mergeCells('AT1:AW2');
            $sheet->setCellValue('AT1', 'Incidents');

            $incidentHeaders = [
                'Partial Boat Loss',
                'Total Boat Loss',
                'Natural Fishermen Deaths',
                'Missing Fishermen at Sea',
            ];

            foreach ($incidentHeaders as $index => $header) {
                $column = Coordinate::stringFromColumnIndex(46 + $index); // AT
                $sheet->setCellValue($column . '3', $header);
            }


            // ------------------------------------------------------------
            // Beach Seines
            // ------------------------------------------------------------

            $sheet->mergeCells('AX1:AX3');
            $sheet->setCellValue('AX1', 'Beach Seines');


            // ------------------------------------------------------------
            // Awareness Programmes
            // ------------------------------------------------------------

            $sheet->mergeCells('AY1:BD2');
            $sheet->setCellValue('AY1', 'Awareness Programmes');

            $awarenessHeaders = [
                'Date',
                'Nature',
                'Participants',
                'Cost (Rs.)',
                'Resource Person',
                'Institution',
            ];

            foreach ($awarenessHeaders as $index => $header) {
                $column = Coordinate::stringFromColumnIndex(51 + $index); // AY
                $sheet->setCellValue($column . '3', $header);
            }

            /*
            * --------------------------------------------------------
            * HEADER STYLE
            * --------------------------------------------------------
            */

            $sheet->getStyle('A1:BD3')
                ->getFont()
                ->setBold(true);

            $sheet->getStyle('A1:BD3')->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
                ->setWrapText(true);
            /*
            * --------------------------------------------------------
            * DATA
            * --------------------------------------------------------
            */

            $row = 4;
            $currentDistrict = null;

            foreach ($submissions as $submission) {

                $districtName = $submission->division->district->name
                    ?? 'Unknown District';

                /*
                * Add district heading whenever district changes.
                */

                if ($currentDistrict !== $districtName) {

                    $sheet->mergeCells("A{$row}:BD{$row}");

                    $sheet->setCellValue(
                        "A{$row}",
                        strtoupper($districtName) . ' DISTRICT'
                    );

                    $sheet->getStyle("A{$row}:BD{$row}")
                        ->getFont()
                        ->setBold(true);

                    $sheet->getStyle("A{$row}:BD{$row}")
                        ->getAlignment()
                        ->setHorizontal(
                            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT
                        );

                    $currentDistrict = $districtName;
                    $row++;
                }

                /*
                * ----------------------------------------------------
                * Default boat values
                * ----------------------------------------------------
                */

                $boats = [
                    'imul_over50' => 0,
                    'imul' => 0,
                    'iday' => 0,
                    'ofrp' => 0,
                    'mtrb' => 0,
                    'ntrb' => 0,
                    'nbsb' => 0,
                ];

                foreach ($submission->boats as $boat) {

                    if (isset($boats[$boat->craft_type])) {

                        $boats[$boat->craft_type] =
                            (int) $boat->boat_count;
                    }
                }

                /*
                * ----------------------------------------------------
                * Registrations
                * ----------------------------------------------------
                */

                $registrations = [];

                foreach ($registrationCrafts as $craft) {

                    foreach ([
                        'first',
                        'renewal',
                        'cancellation',
                    ] as $action) {

                        $registrations[$action][$craft['key']] = 0;
                    }
                }

                foreach ($submission->registrations as $registration) {

                    if (
                        isset(
                            $registrations[$registration->action]
                                [$registration->craft_type]
                        )
                    ) {

                        $registrations[$registration->action]
                            [$registration->craft_type]
                            = (int) $registration->reg_count;
                    }
                }

                /*
                * ----------------------------------------------------
                * Licences with craft
                * ----------------------------------------------------
                */

                $licenses = [
                    'imul' => 0,
                    'iday' => 0,
                    'ofrp' => 0,
                    'mtrb' => 0,
                    'ntrb' => 0,
                    'nbsb' => 0,
                ];

                foreach ($submission->licensesWithCraft as $license) {

                    if (isset($licenses[$license->craft_type])) {

                        $licenses[$license->craft_type] =
                            (int) $license->license_count;
                    }
                }

                /*
                * ----------------------------------------------------
                * Awareness programmes
                * ----------------------------------------------------
                */

                $awarenessDates = [];
                $awarenessNature = [];
                $awarenessParticipants = [];
                $awarenessCosts = [];
                $awarenessResources = [];
                $awarenessInstitutions = [];

                foreach ($submission->awarenessProgrammes as $programme) {

                    $awarenessDates[] =
                        $programme->event_date ?? '';

                    $awarenessNature[] =
                        $programme->nature ?? '';

                    $awarenessParticipants[] =
                        (int) $programme->participants;

                    $awarenessCosts[] =
                        number_format(
                            (float) $programme->cost,
                            2
                        );

                    $awarenessResources[] =
                        $programme->resource_person ?? '';

                    $awarenessInstitutions[] =
                        $programme->institution ?? '';
                }

                /*
                * ----------------------------------------------------
                * Write main row
                * ----------------------------------------------------
                */

                $sheet->setCellValue(
                    "A{$row}",
                    $districtName
                );

                $sheet->setCellValue(
                    "B{$row}",
                    $submission->division->name ?? ''
                );

                /*
                * Community Profile
                */

                $sheet->setCellValue(
                    "C{$row}",
                    (int) $submission->families
                );

                $sheet->setCellValue(
                    "D{$row}",
                    (int) $submission->active_fishermen
                );

                $sheet->setCellValue(
                    "E{$row}",
                    (int) $submission->population
                );

                /*
                * Existing Boats
                */

                $sheet->setCellValue("F{$row}", $boats['imul_over50']);
                $sheet->setCellValue("G{$row}", $boats['imul']);
                $sheet->setCellValue("H{$row}", $boats['iday']);
                $sheet->setCellValue("I{$row}", $boats['ofrp']);
                $sheet->setCellValue("J{$row}", $boats['mtrb']);
                $sheet->setCellValue("K{$row}", $boats['ntrb']);
                $sheet->setCellValue("L{$row}", $boats['nbsb']);

                /*
                * ----------------------------------------------------
                * Registrations
                *
                * M:S  = First Registrations
                * T:Z  = Renewals
                * AA:AG = Cancellations
                * ----------------------------------------------------
                */

                $column = 13; // M

                // First Registrations
                foreach ($registrationCrafts as $craft) {

                    $sheet->setCellValue(
                        Coordinate::stringFromColumnIndex($column) . $row,
                        $registrations['first'][$craft['key']]
                    );

                    $column++;
                }

                // Renewals
                foreach ($registrationCrafts as $craft) {

                    $sheet->setCellValue(
                        Coordinate::stringFromColumnIndex($column) . $row,
                        $registrations['renewal'][$craft['key']]
                    );

                    $column++;
                }

                // Cancellations
                foreach ($registrationCrafts as $craft) {

                    $sheet->setCellValue(
                        Coordinate::stringFromColumnIndex($column) . $row,
                        $registrations['cancellation'][$craft['key']]
                    );

                    $column++;
                }

                /*
                * ----------------------------------------------------
                * Licences
                * AH:AP
                * ----------------------------------------------------
                */

                $sheet->setCellValue(
                    "AH{$row}",
                    (int) $submission->licenses_without_craft
                );

                $sheet->setCellValue("AI{$row}", $licenses['imul']);
                $sheet->setCellValue("AJ{$row}", $licenses['iday']);
                $sheet->setCellValue("AK{$row}", $licenses['ofrp']);
                $sheet->setCellValue("AL{$row}", $licenses['mtrb']);
                $sheet->setCellValue("AM{$row}", $licenses['ntrb']);
                $sheet->setCellValue("AN{$row}", $licenses['nbsb']);

                $totalWithCraft = array_sum($licenses);

                $sheet->setCellValue(
                    "AO{$row}",
                    $totalWithCraft
                );

                $sheet->setCellValue(
                    "AP{$row}",
                    (int) $submission->licenses_without_craft
                    + $totalWithCraft
                );

                /*
                * ----------------------------------------------------
                * Migrated Boats
                * AQ:AS
                * ----------------------------------------------------
                */

                $sheet->setCellValue(
                    "AQ{$row}",
                    (int) $submission->migrated_imul
                );

                $sheet->setCellValue(
                    "AR{$row}",
                    (int) $submission->migrated_iday
                );

                $sheet->setCellValue(
                    "AS{$row}",
                    (int) $submission->migrated_ofrp
                );

                /*
                * ----------------------------------------------------
                * Incidents
                * AT:AW
                * ----------------------------------------------------
                */

                $sheet->setCellValue(
                    "AT{$row}",
                    (int) $submission->incident_partial_loss
                );

                $sheet->setCellValue(
                    "AU{$row}",
                    (int) $submission->incident_total_loss
                );

                $sheet->setCellValue(
                    "AV{$row}",
                    (int) $submission->incident_natural_deaths
                );

                $sheet->setCellValue(
                    "AW{$row}",
                    (int) $submission->incident_missing
                );

                /*
                * ----------------------------------------------------
                * Beach Seines
                * ----------------------------------------------------
                */

                $sheet->setCellValue(
                    "AX{$row}",
                    (int) $submission->beach_seines
                );

                /*
                * ----------------------------------------------------
                * Awareness Programmes
                * ----------------------------------------------------
                */

                $sheet->setCellValue(
                    "AY{$row}",
                    implode("\n", $awarenessDates)
                );

                $sheet->setCellValue(
                    "AZ{$row}",
                    implode("\n", $awarenessNature)
                );

                $sheet->setCellValue(
                    "BA{$row}",
                    implode("\n", $awarenessParticipants)
                );

                $sheet->setCellValue(
                    "BB{$row}",
                    implode("\n", $awarenessCosts)
                );

                $sheet->setCellValue(
                    "BC{$row}",
                    implode("\n", $awarenessResources)
                );

                $sheet->setCellValue(
                    "BD{$row}",
                    implode("\n", $awarenessInstitutions)
                );

                $row++;
            }

            /*
            * --------------------------------------------------------
            * TABLE FORMATTING
            * --------------------------------------------------------
            */

            $lastRow = $row - 1;

            $sheet->getStyle("A1:BD{$lastRow}")
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(
                    \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                );

            $sheet->getStyle("A4:BD{$lastRow}")
                ->getAlignment()
                ->setVertical(
                    \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
                );

            /*
            * Wrap awareness cells
            */

            $sheet->getStyle("AY4:BD{$lastRow}")
                ->getAlignment()
                ->setWrapText(true);

            /*
            * Freeze panes
            */

            $sheet->freezePane('C4');

            /*
            * --------------------------------------------------------
            * COLUMN WIDTHS
            * --------------------------------------------------------
            */

            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(22);

            // Community Profile
            foreach (['C', 'D', 'E'] as $column) {
                $sheet->getColumnDimension($column)->setWidth(14);
            }

            // Existing Operated Boats
            foreach ([
                'F', 'G', 'H', 'I', 'J', 'K', 'L'
            ] as $column) {
                $sheet->getColumnDimension($column)->setWidth(10);
            }

            // Boat Registrations - narrower
            foreach ([
                'M', 'N', 'O', 'P', 'Q', 'R', 'S',
                'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
                'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG'
            ] as $column) {
                $sheet->getColumnDimension($column)->setWidth(9);
            }

            // Licences
            foreach ([
                'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP'
            ] as $column) {
                $sheet->getColumnDimension($column)->setWidth(12);
            }

            // Migrated Boats
            foreach (['AQ', 'AR', 'AS'] as $column) {
                $sheet->getColumnDimension($column)->setWidth(10);
            }

            // Incidents
            foreach (['AT', 'AU', 'AV', 'AW'] as $column) {
                $sheet->getColumnDimension($column)->setWidth(22);
            }

            // Beach Seines
            $sheet->getColumnDimension('AX')->setWidth(14);

            // Awareness Programmes
            foreach ([
                'AY', 'AZ', 'BA', 'BB', 'BC', 'BD'
            ] as $column) {
                $sheet->getColumnDimension($column)->setWidth(22);
            }

            /*
            * --------------------------------------------------------
            * ROW HEIGHTS
            * --------------------------------------------------------
            */

            $sheet->getRowDimension(1)->setRowHeight(30);
            $sheet->getRowDimension(2)->setRowHeight(30);
            $sheet->getRowDimension(3)->setRowHeight(45);

            /*
            * --------------------------------------------------------
            * DOWNLOAD
            * --------------------------------------------------------
            */

            $filename =
                'BSC-1_National_' .
                date('Y-m', strtotime($periodDate)) .
                '.xlsx';

            header(
                'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            );

            header(
                'Content-Disposition: attachment;filename="' .
                $filename .
                '"'
            );

            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);

            $writer->save('php://output');

            exit;
        }

        // BSC-2 EXCEL EXPORT
        if ($form === 'bsc2') {

            $submissions = Bsc2Submission::find()
                ->with([
                    'division',
                    'division.district',
                    'seaworthinessCerts',
                    'boatsInsured',
                    'lagoonActivities',
                ])
                ->joinWith('division')
                ->where([
                    'period_date' => $periodDate,
                    'approval_stage' => (string) Constant::DEVELOPMENT_DIVISION,
                ])
                ->orderBy([
                    'm_division.district_id' => SORT_ASC,
                    'm_division.name' => SORT_ASC,
                ])
                ->all();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setTitle('BSC-2 National');

            /*
            * ---------------------------------------------------------
            * HEADER ROWS
            * ---------------------------------------------------------
            */

            // Row 1 = grouped headings
            // Row 2 = individual field headings

            $sheet->mergeCells('A1:A2');
            $sheet->mergeCells('B1:B2');

            $sheet->mergeCells('C1:C2'); // Beach Seine Licenses

            // Sea-worthiness
            $sheet->mergeCells('D1:E1');

            // Fishing Boats Insured
            $sheet->mergeCells('F1:I1');

            // Fish Production
            $sheet->mergeCells('J1:M1');

            // Legal & Enforcement
            $sheet->mergeCells('N1:O1');

            // Fishermen Registration
            $sheet->mergeCells('P1:Q1');

            // Welfare & Awareness
            $sheet->mergeCells('R1:T1');

            // Fishing Operations
            $sheet->mergeCells('U1:V1');

            // Marine Animal Deaths
            $sheet->mergeCells('W1:X1');

            // Lagoon activities
            $sheet->mergeCells('Y1:Y2');


            /*
            * Row 1
            */

            $sheet->setCellValue('A1', 'District');
            $sheet->setCellValue('B1', 'F.I. Division');
            $sheet->setCellValue('C1', 'Beach Seine Licenses');

            $sheet->setCellValue('D1', 'Sea-worthiness Certificates');

            $sheet->setCellValue('F1', 'Fishing Boats Insured');

            $sheet->setCellValue('J1', 'Fish Production');

            $sheet->setCellValue('N1', 'Legal & Enforcement Activities');

            $sheet->setCellValue('P1', 'Fishermen Registration & Identification');

            $sheet->setCellValue('R1', 'Fishermen Welfare & Awareness');

            $sheet->setCellValue('U1', 'Fishing Operations');

            $sheet->setCellValue('W1', 'Recorded Marine Animal Deaths');

            $sheet->setCellValue('Y1', 'Lagoon Management Activities');


            /*
            * Row 2
            */

            $sheet->setCellValue('D2', 'Inboard');
            $sheet->setCellValue('E2', 'Outboard');

            $sheet->setCellValue('F2', 'IMUL');
            $sheet->setCellValue('G2', 'IDAY');
            $sheet->setCellValue('H2', 'OFRP');
            $sheet->setCellValue('I2', 'MTRB');

            $sheet->setCellValue('J2', 'Lagoon & Brackish Water');
            $sheet->setCellValue('K2', 'Coastal');
            $sheet->setCellValue('L2', 'Offshore');
            $sheet->setCellValue('M2', 'Total');

            $sheet->setCellValue('N2', 'No. of Raids');
            $sheet->setCellValue('O2', 'No. of Court Cases');

            $sheet->setCellValue('P2', 'Fisherman Registrations');
            $sheet->setCellValue('Q2', 'I.D. Cards Issued');

            $sheet->setCellValue('R2', 'Awareness Programmes');
            $sheet->setCellValue('S2', 'Insurance Enrolled');
            $sheet->setCellValue('T2', 'Pension Enrolled');

            $sheet->setCellValue('U2', 'Log Sheets Collected');
            $sheet->setCellValue('V2', 'No. of Departures');

            $sheet->setCellValue('W2', 'Marine Mammal Deaths');
            $sheet->setCellValue('X2', 'Turtle Deaths');


            /*
            * ---------------------------------------------------------
            * HEADER FORMATTING
            * ---------------------------------------------------------
            */

            $lastColumn = 'Y';

            $sheet->getStyle("A1:{$lastColumn}2")->getFont()->setBold(true);

            $sheet->getStyle("A1:{$lastColumn}2")
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
                ->setWrapText(true);

            $sheet->getRowDimension(1)->setRowHeight(35);
            $sheet->getRowDimension(2)->setRowHeight(40);

            /*
            * ---------------------------------------------------------
            * DATA
            * ---------------------------------------------------------
            */

            $row = 3;

            $currentDistrictId = null;

            foreach ($submissions as $submission) {

                $division = $submission->division;

                if (!$division) {
                    continue;
                }

                /*
                * District heading
                *
                * Add a separate district row whenever
                * the district changes.
                */
                if ($currentDistrictId !== $division->district_id) {

                    if ($currentDistrictId !== null) {
                        $row++;
                    }

                    $sheet->mergeCells("A{$row}:{$lastColumn}{$row}");

                    $sheet->setCellValue(
                        "A{$row}",
                        strtoupper($division->district->name ?? 'Unknown District')
                        . ' DISTRICT'
                    );

                    $sheet->getStyle("A{$row}:{$lastColumn}{$row}")
                        ->getFont()
                        ->setBold(true);

                    $row++;

                    $currentDistrictId = $division->district_id;
                }


                /*
                * -----------------------------------------------------
                * Sea-worthiness certificates
                * -----------------------------------------------------
                */

                $seaworthiness = [
                    'inboard' => 0,
                    'outboard' => 0,
                ];

                foreach ($submission->seaworthinessCerts as $certificate) {

                    if (isset($seaworthiness[$certificate->category])) {
                        $seaworthiness[$certificate->category]
                            = (int) $certificate->cert_count;
                    }
                }


                /*
                * -----------------------------------------------------
                * Boats insured
                * -----------------------------------------------------
                */

                $boatsInsured = [
                    'imul' => 0,
                    'iday' => 0,
                    'ofrp' => 0,
                    'mtrb' => 0,
                ];

                foreach ($submission->boatsInsured as $boat) {

                    if (isset($boatsInsured[$boat->craft_type])) {
                        $boatsInsured[$boat->craft_type]
                            = (int) $boat->insured_count;
                    }
                }


                /*
                * -----------------------------------------------------
                * Lagoon activities
                * -----------------------------------------------------
                */

                $lagoonActivities = [];

                foreach ($submission->lagoonActivities as $activity) {

                    $lagoonName = trim((string) $activity->lagoon_name);
                    $activityText = trim((string) $activity->activity);

                    if ($lagoonName !== '' || $activityText !== '') {

                        $lagoonActivities[] =
                            ($lagoonName !== '' ? $lagoonName : 'Lagoon')
                            . ': '
                            . $activityText;
                    }
                }

                $lagoonText = implode("\n", $lagoonActivities);


                /*
                * -----------------------------------------------------
                * Write division row
                * -----------------------------------------------------
                */

                $sheet->setCellValue("A{$row}", $division->district->name ?? '');
                $sheet->setCellValue("B{$row}", $division->name);

                // Licences
                $sheet->setCellValue(
                    "C{$row}",
                    (int) $submission->beach_seine_licenses
                );

                // Sea-worthiness
                $sheet->setCellValue(
                    "D{$row}",
                    $seaworthiness['inboard']
                );

                $sheet->setCellValue(
                    "E{$row}",
                    $seaworthiness['outboard']
                );

                // Boats insured
                $sheet->setCellValue(
                    "F{$row}",
                    $boatsInsured['imul']
                );

                $sheet->setCellValue(
                    "G{$row}",
                    $boatsInsured['iday']
                );

                $sheet->setCellValue(
                    "H{$row}",
                    $boatsInsured['ofrp']
                );

                $sheet->setCellValue(
                    "I{$row}",
                    $boatsInsured['mtrb']
                );

                // Production
                $sheet->setCellValue(
                    "J{$row}",
                    (float) $submission->production_lagoon
                );

                $sheet->setCellValue(
                    "K{$row}",
                    (float) $submission->production_coastal
                );

                $sheet->setCellValue(
                    "L{$row}",
                    (float) $submission->production_offshore
                );

                $sheet->setCellValue(
                    "M{$row}",
                    (float) $submission->getTotalProduction()
                );

                // Legal
                $sheet->setCellValue(
                    "N{$row}",
                    (int) $submission->no_of_raids
                );

                $sheet->setCellValue(
                    "O{$row}",
                    (int) $submission->no_of_court_cases
                );

                // Registration
                $sheet->setCellValue(
                    "P{$row}",
                    (int) $submission->fishermen_registered
                );

                $sheet->setCellValue(
                    "Q{$row}",
                    (int) $submission->id_cards_issued
                );

                // Welfare
                $sheet->setCellValue(
                    "R{$row}",
                    (int) $submission->awareness_programmes
                );

                $sheet->setCellValue(
                    "S{$row}",
                    (int) $submission->insurance_enrolled
                );

                $sheet->setCellValue(
                    "T{$row}",
                    (int) $submission->pension_enrolled
                );

                // Fishing operations
                $sheet->setCellValue(
                    "U{$row}",
                    (int) $submission->log_sheets_collected
                );

                $sheet->setCellValue(
                    "V{$row}",
                    (int) $submission->departures
                );

                // Marine animal deaths
                $sheet->setCellValue(
                    "W{$row}",
                    (int) $submission->recorded_marine_mammal_deaths
                );

                $sheet->setCellValue(
                    "X{$row}",
                    (int) $submission->recorded_turtle_deaths
                );

                // Lagoon activities
                $sheet->setCellValue(
                    "Y{$row}",
                    $lagoonText
                );

                $row++;
            }

            /*
            * ---------------------------------------------------------
            * DATA FORMATTING
            * ---------------------------------------------------------
            */

            $lastDataRow = $row - 1;

            if ($lastDataRow >= 3) {

                $sheet->getStyle("A3:{$lastColumn}{$lastDataRow}")
                    ->getAlignment()
                    ->setVertical(
                        \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
                    );

                $sheet->getStyle("C3:X{$lastDataRow}")
                    ->getAlignment()
                    ->setHorizontal(
                        \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT
                    );

                $sheet->getStyle("Y3:Y{$lastDataRow}")
                    ->getAlignment()
                    ->setWrapText(true);
            }


            /*
            * ---------------------------------------------------------
            * Borders
            * ---------------------------------------------------------
            */

            $sheet->getStyle("A1:{$lastColumn}{$lastDataRow}")
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(
                    \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                );


            /*
            * ---------------------------------------------------------
            * Freeze header
            * ---------------------------------------------------------
            */

            $sheet->freezePane('C3');


            /*
            * ---------------------------------------------------------
            * Column widths
            * ---------------------------------------------------------
            */

            $widths = [
                'A' => 18,
                'B' => 25,
                'C' => 18,
                'D' => 14,
                'E' => 14,
                'F' => 12,
                'G' => 12,
                'H' => 12,
                'I' => 12,
                'J' => 18,
                'K' => 14,
                'L' => 14,
                'M' => 14,
                'N' => 14,
                'O' => 16,
                'P' => 20,
                'Q' => 16,
                'R' => 20,
                'S' => 18,
                'T' => 18,
                'U' => 18,
                'V' => 16,
                'W' => 20,
                'X' => 16,
                'Y' => 35,
            ];

            foreach ($widths as $column => $width) {
                $sheet->getColumnDimension($column)->setWidth($width);
            }


            /*
            * ---------------------------------------------------------
            * Download
            * ---------------------------------------------------------
            */

            $filename =
                'BSC2_National_' .
                date('Y-m', strtotime($periodDate)) .
                '.xlsx';

            header(
                'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            );

            header(
                'Content-Disposition: attachment;filename="' .
                $filename .
                '"'
            );

            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);

            $writer->save('php://output');

            exit;
        }
    }
/////////////////////////////////////////////////


    // ============================================================
    // Helpers
    // ============================================================

    private function requireType($type)
    {
        if (!UserTypeUtil::hasType($type)) {
            throw new ForbiddenHttpException();
        }
    }

    private function resolvePeriodDate($period)
    {
        if ($period) {
            return date('Y-m-01', strtotime($period . '-01'));
        }
        return date('Y-m-01');
    }

    /**
     * Builds [byDistrict, headers, grandTotals], shared by both
     * actionNational() and actionExportExcel() so the on-screen report and
     * the Excel download can never show different numbers from each other.
     *
     * byDistrict: [districtId => [
     *   'name' => districtName,
     *   'rows' => [['name' => divisionName, 'values' => [...]], ...],
     *   'totals' => [...],
     * ]]
     *
     * Only includes submissions with approval_stage == Constant::DEVELOPMENT_DIVISION
     * (fully validated by the Divisional AD), per the confirmed workflow.
     */
    private function buildNationalReportData($form, $periodDate)
    {
        $modelClass = $form === 'bsc2' ? Bsc2Submission::class : Bsc1Submission::class;
        $relations = $form === 'bsc1'
            ? ['division', 'boats', 'registrations', 'licensesWithCraft', 'awarenessProgrammes']
            : ['division', 'seaworthinessCerts', 'boatsInsured', 'lagoonActivities'];

        $headers = $form === 'bsc1'
            ? ['Families', 'Active Fishermen', 'Population', 'Total Boats', 'Total Registrations', 'Total Licenses', 'Migrated Boats', 'Partial Loss', 'Total Loss', 'Deaths', 'Missing', 'Awareness Programmes']
            : ['Beach Seine Licenses','Fishing Boats Insured', 'Total Production (Mt)', 'Log Sheets', 'Departures', 'Raids', 'Court Cases'];

        $submissions = $modelClass::find()
            ->with($relations)
            ->where(['period_date' => $periodDate])
            ->andWhere(['approval_stage' => (string) Constant::DEVELOPMENT_DIVISION]) // only validated submissions
            ->all();

        $districtIds = array_values(array_unique(array_filter(array_map(function ($s) {
            return $s->division->district_id ?? null;
        }, $submissions))));
        $districts = MFiDistrict::find()->where(['id' => $districtIds])->indexBy('id')->all();

        $byDistrict = [];
        $grandTotals = array_fill(0, count($headers), 0);

        foreach ($submissions as $s) {
            $did = $s->division->district_id ?? 0;
            if (!isset($byDistrict[$did])) {
                $byDistrict[$did] = [
                    'id' => $did,
                    'name' => $districts[$did]->name ?? 'Unknown District',
                    'rows' => [],
                    'totals' => array_fill(0, count($headers), 0),
                ];
            }
            $values = $this->bscRowValues($s, $form);
            $byDistrict[$did]['rows'][] = ['name' => $s->division->name ?? '', 'values' => $values];
            foreach ($values as $i => $v) {
                $byDistrict[$did]['totals'][$i] += $v;
                $grandTotals[$i] += $v;
            }
        }
        ksort($byDistrict);

        return [$byDistrict, $headers, $grandTotals];
    }

    /**
     * Returns one submission's numeric row values, in the exact order the
     * headers in buildNationalReportData() expect. Kept as one method so
     * the print view and the Excel export read from a single source.
     */
    private function bscRowValues($s, $form)
    {
        if ($form === 'bsc1') {
            $totalBoats = array_sum(array_map(function ($b) { return $b->boat_count; }, $s->boats));
            $totalReg = array_sum(array_map(function ($r) { return $r->reg_count; }, $s->registrations));
            $totalLic = array_sum(array_map(function ($l) { return $l->license_count; }, $s->licensesWithCraft))
                + (int) $s->licenses_without_craft;
            $migrated = (int) $s->migrated_imul + (int) $s->migrated_iday + (int) $s->migrated_ofrp;

            return [
                (int) $s->families, (int) $s->active_fishermen, (int) $s->population,
                $totalBoats, $totalReg, $totalLic, $migrated,
                (int) $s->incident_partial_loss, (int) $s->incident_total_loss,
                (int) $s->incident_natural_deaths, (int) $s->incident_missing,
                count($s->awarenessProgrammes),
            ];
        }

        $totalInsured = 0;

        foreach ($s->boatsInsured as $boat) {
            $totalInsured += (int) $boat->insured_count;
        }

        return [

                (int) $s->beach_seine_licenses,
                $totalInsured,
                (float) $s->getTotalProduction(),
                (int) $s->log_sheets_collected,
                (int) $s->no_of_raids,
                (int) $s->no_of_court_cases,
                (int) $s->departures,

        ];
    }

    /**
     * Shows division-level details for one district.
     *
     * @param string $form
     * @param int $district
     * @param string|null $period
     */
    public function actionDistrict($form = 'bsc1', $district = null, $period = null)
    {
        $this->requireType(Constant::DEVELOPMENT_DIVISION);

        $periodDate = $this->resolvePeriodDate($period);

        $modelClass = $form === 'bsc2'
            ? Bsc2Submission::class
            : Bsc1Submission::class;

        $relations = $form === 'bsc1'
            ? ['division', 'boats', 'registrations', 'licensesWithCraft', 'awarenessProgrammes']
            : ['division', 'seaworthinessCerts', 'boatsInsured', 'lagoonActivities'];

        $submissions = $modelClass::find()
            ->with($relations)
            ->joinWith('division')
            ->where([
                'period_date' => $periodDate,
                'm_division.district_id' => $district,
                'approval_stage' => (string) Constant::DEVELOPMENT_DIVISION,
            ])
            ->all();

        $districtName = MFiDistrict::find()
            ->select('name')
            ->where(['id' => $district])
            ->scalar();

        return $this->render('district-details-' . $form, [
            'form' => $form,
            'periodDate' => $periodDate,
            'district' => $district,
            'submissions' => $submissions,
            'districtName' => $districtName,
        ]);
    }
}