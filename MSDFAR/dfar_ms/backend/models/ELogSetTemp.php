<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "e_log_set_temp".
 *
 * @property int $id
 * @property int|null $elog_id
 * @property string|null $gear_type
 * @property int|null $set_number
 * @property string|null $start_datetime
 * @property string|null $start_gps_direction
 * @property float|null $start_gps_n
 * @property float|null $start_gps_e
 * @property string|null $end_datetime
 * @property string|null $end_gps_direction
 * @property float|null $end_gps_n
 * @property float|null $end_gps_e
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property ELogTemp $elog
 * @property ELogCatchTemp[] $catches
 */
class ELogSetTemp extends ActiveRecord
{
    const GEAR_RINGNET  = 'ringnet';
    const GEAR_LONGLINE = 'longline';
    const GEAR_GILLNET  = 'gillnet';

    const DIR_NORTH = 'N';
    const DIR_SOUTH = 'S';

    /**
     * Allowed E values for each N degree, per direction.
     * Mirrors the gpsMap used client-side.
     */
    private static function gpsMap()
    {
        return [
            'N' => [
                0 => [48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93],
                1 => [49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92],
                2 => [50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92],
                3 => [50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92],
                4 => [51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91],
                5 => [52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,77,78,79,80,81,82,83,84,85,86,87,88,89,90],
                6 => [52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,77,78,79,80,81,82,83,84,85,86,87,88,89],
                7 => [53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,78,79,81,82,83,84,85,86,87,88,89],
                8 => [53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,78,79,80,81,82,83,84,85,86,87,88,89],
                9 => [54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,79,80,81,82,83,84,85,86,87,88,89],
                10 => [56,57,58,59,60,61,62,63,64,65,66,67,68,79,80,81,82,83,84,85,86,87,88],
                11 => [57,58,59,60,61,62,63,64,65,66,67,68,81,82,83,84,85,86,87,88],
                12 => [57,58,59,60,61,62,63,64,65,66,67,68,83,84,85,86,87,88,89],
                13 => [57,58,59,60,61,62,63,64,65,66,67,68,83,84,85,86,87,88,89],
                14 => [57,58,59,60,61,62,63,64,65,66,67,68,69,84,85,86,87,88,89],
                15 => [58,59,60,61,62,63,64,65,66,67,68,69,85,86,87,88,89,90],
                16 => [59,60,61,62,63,64,65,66,67,68,69,86,87,88,89,90],
                17 => [60,61,62,63,64,65,66,67,68,69,88,89,90],
                18 => [61,62,63,64,65,66,67,68,89,90],
                19 => [62,63,64,65,66,67],
                20 => [62,63,64,65,66],
                21 => [63],
            ],
            'S' => [
                0 => [46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93],
                1 => [46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94],
                2 => [45,46,47,48,49,50,51,52,53,57,58,59,60,61,62,63,64,65,66,67,68,69,70,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95],
                3 => [44,45,46,47,48,49,50,51,58,59,60,61,62,63,64,65,66,67,68,69,70,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95],
                4 => [44,45,46,47,48,49,50,58,59,60,61,62,63,64,65,66,67,68,69,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97],
                5 => [43,44,45,46,47,48,49,50,59,60,61,62,63,64,65,66,67,68,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98],
                6 => [43,44,45,46,47,48,49,59,60,61,62,63,64,65,66,67,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98],
                7 => [43,44,45,46,47,48,49,59,60,61,62,63,64,65,66,67,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98],
                8 => [43,59,60,61,62,63,64,65,66,67,68,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99],
                9 => [59,60,61,62,63,64,65,66,67,68,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100],
                10 => [60,61,62,63,64,65,66,67,68,69,70,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94],
                11 => [59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93],
                12 => [59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93],
                13 => [60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93],
                14 => [61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93],
                15 => [62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93],
                16 => [62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94],
                17 => [63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95],
                18 => [65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100],
                19 => [66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100],
                20 => [66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'e_log_set_temp';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['elog_id', 'set_number'], 'integer'],
            [['gear_type'], 'string', 'max' => 20],
            [['gear_type'], 'in', 'range' => [self::GEAR_RINGNET, self::GEAR_LONGLINE, self::GEAR_GILLNET]],

            [['elog_id', 'gear_type', 'set_number'], 'required'],

            [['start_datetime', 'end_datetime'], 'safe'],
            [['start_gps_direction', 'end_gps_direction'], 'string', 'max' => 1],
            [['start_gps_direction', 'end_gps_direction'], 'in', 'range' => [self::DIR_NORTH, self::DIR_SOUTH]],

            [['start_datetime', 'start_gps_direction', 'start_gps_n', 'start_gps_e'], 'required'],

            [['start_gps_n', 'start_gps_e', 'end_gps_n', 'end_gps_e'], 'number'],

            // Minutes portion of N/E must be .00–.59 (nautical minutes, not decimal degrees)
            [['start_gps_n', 'start_gps_e'], 'validateMinutesRange'],
            [['end_gps_n', 'end_gps_e'], 'validateMinutesRange'],

            // N/E combination must exist in the allowed gpsMap for the chosen direction
            ['start_gps_e', 'validateGpsCombo', 'params' => ['prefix' => 'start']],
            ['end_gps_e', 'validateGpsCombo', 'params' => ['prefix' => 'end']],

            ['end_datetime', 'compare', 'compareAttribute' => 'start_datetime', 'operator' => '>', 'type' => 'string', 'message' => 'End DateTime must be later than Start DateTime.'],

            [['created_at', 'updated_at'], 'safe'],

            [['elog_id'], 'exist', 'skipOnError' => true, 'targetClass' => ELogTemp::class, 'targetAttribute' => ['elog_id' => 'id']],
        ];
    }

    /**
     * Validates that the decimal ("minutes") portion of a GPS value is between .00 and .59.
     * Mirrors hasInvalidMinutes() in the front-end JS.
     */
    public function validateMinutesRange($attribute, $params)
    {
        $value = $this->$attribute;

        if ($value === null || $value === '') {
            return;
        }

        $str = (string) $value;
        $dotPos = strpos($str, '.');

        if ($dotPos === false) {
            return; // whole number, no minutes portion
        }

        $decimals = substr($str, $dotPos + 1);
        $minutes = (int) str_pad(substr($decimals, 0, 2), 2, '0');

        if ($minutes >= 60) {
            $this->addError($attribute, 'Decimal portion must represent minutes between .00 and .59.');
        }
    }

    /**
     * Validates that the given direction/N/E combination is an allowed coordinate,
     * mirroring the client-side gpsMap lookup.
     *
     * @param string $attribute the E attribute being validated (e.g. start_gps_e)
     * @param array $params ['prefix' => 'start'|'end']
     */
    public function validateGpsCombo($attribute, $params)
    {
        $prefix = $params['prefix'];

        $dirAttr = $prefix . '_gps_direction';
        $nAttr   = $prefix . '_gps_n';
        $eAttr   = $prefix . '_gps_e';

        $dir = $this->$dirAttr;
        $n   = $this->$nAttr;
        $e   = $this->$eAttr;

        // Skip if any required piece is missing — required/number rules will catch that separately
        if ($dir === null || $dir === '' || $n === null || $n === '' || $e === null || $e === '') {
            return;
        }

        $map = self::gpsMap();

        if (!isset($map[$dir])) {
            $this->addError($dirAttr, 'Invalid GPS direction.');
            return;
        }

        $nKey = (int) floor((float) $n);

        if (!isset($map[$dir][$nKey])) {
            $this->addError($nAttr, "No allowed GPS E range exists for {$dir} {$nKey}.");
            return;
        }

        $eKey = (int) floor((float) $e);
        $allowed = $map[$dir][$nKey];

        if (!in_array($eKey, $allowed, true)) {
            sort($allowed);
            $this->addError(
                $eAttr,
                "GPS E {$eKey} is out of range for {$dir} {$nKey}. Allowed: " . implode(', ', $allowed) . '.'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'elog_id' => 'E-Log',
            'gear_type' => 'Gear Type',
            'set_number' => 'Set Number',
            'start_datetime' => 'Start DateTime',
            'start_gps_direction' => 'Start GPS Direction',
            'start_gps_n' => 'Start GPS N',
            'start_gps_e' => 'Start GPS E',
            'end_datetime' => 'End DateTime',
            'end_gps_direction' => 'End GPS Direction',
            'end_gps_n' => 'End GPS N',
            'end_gps_e' => 'End GPS E',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Elog]].
     *
     * @return ActiveQuery
     */
    public function getElog()
    {
        return $this->hasOne(ELogTemp::class, ['id' => 'elog_id']);
    }

    /**
     * Gets query for [[Catches]].
     *
     * @return ActiveQuery
     */
    public function getCatches()
    {
        return $this->hasMany(ELogCatchTemp::class, ['e_log_set_temp_id' => 'id']);
    }
}