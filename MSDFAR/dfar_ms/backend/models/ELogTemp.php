<?php

namespace backend\models;
use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class ELogTemp extends ActiveRecord
{
    const GEAR_RINGNET  = 'ringnet';
    const GEAR_LONGLINE = 'longline';
    const GEAR_GILLNET  = 'gillnet';

    const HOOK_J36 = 'J36';
    const HOOK_J26 = 'J26';
    const HOOK_O83 = 'O83';
    const HOOK_O17 = 'O17';

    const BAIT_SQUID      = 'squid';
    const BAIT_FLYINGFISH = 'flyingfish';
    const BAIT_MILKFISH   = 'milkfish';
    const BAIT_INDIANSCAD = 'indianscad';
    const BAIT_OTHER      = 'other';

    const MAT_NYLON_BRAIDED       = 'nylon_braided';
    const MAT_NYLON_MULTIFILAMENT = 'nylon_multifilament';
    const MAT_OTHER               = 'other';

    public static function tableName()
    {
        return 'e_log_temp';
    }
     public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'), // string DATETIME, not unix int
            ],
        ];
    }
    public function rules()
    {
        return [
            // Base required fields — always required regardless of gear type
            [['vessel_id', 'arrival_date', 'arrival_harbour', 'departure_harbour', 'gear_type'], 'required'],
            [['vessel_id', 'phone_number'], 'string'],
            [['arrival_date', 'departure_date'], 'safe'],
            [['approve'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            ['gear_type', 'in', 'range' => array_keys(self::gearTypeList())],
            ['arrival_date', 'compare', 'compareAttribute' => 'departure_date', 'operator' => '>', 'type' => 'date', 'message' => 'Arrival Date must be later than Departure Date.'],

            // ===== Ring Net — required only when gear_type = ringnet =====
            [['net_length', 'net_height'], 'required', 'when' => function ($model) {
                return $model->gear_type === self::GEAR_RINGNET;
            }, 'whenClient' => "function (attribute, value) {
                return $('#elogtemp-gear_type').val() === 'ringnet';
            }"],
            [['net_length', 'net_height'], 'number'],
            [['fad'], 'safe'],

            // ===== Longline — required only when gear_type = longline =====
            [['mainline', 'branchline', 'no_of_hooks', 'bait', 'no_hook_bet'], 'required', 'when' => function ($model) {
                return $model->gear_type === self::GEAR_LONGLINE;
            }, 'whenClient' => "function (attribute, value) {
                return $('#elogtemp-gear_type').val() === 'longline';
            }"],
            [['mainline', 'branchline', 'depth'], 'number'],
            [['no_of_hooks', 'no_hook_bet'], 'integer'],
            ['hook_type', 'in', 'range' => array_keys(self::hookTypeList())],
            ['bait', 'in', 'range' => array_keys(self::baitList())],

            // ===== Gillnet — required only when gear_type = gillnet =====
            [['mesh_size', 'set_depth', 'length'], 'required', 'when' => function ($model) {
                return $model->gear_type === self::GEAR_GILLNET;
            }, 'whenClient' => "function (attribute, value) {
                return $('#elogtemp-gear_type').val() === 'gillnet';
            }"],
            [['mesh_size', 'net_height', 'set_depth', 'length'], 'number'],
            [['ply', 'net_pieces'], 'integer'],
            ['net_material', 'in', 'range' => array_keys(self::materialList())],
        ];
    }

    public function attributeLabels()
    {
        return [
            'vessel_id'         => 'Vessel',
            'gear_type'         => 'Gear Type',
            'arrival_date'      => 'Arrival Date',
            'arrival_harbour'   => 'Arrival Harbour',
            'departure_date'    => 'Departure Date',
            'departure_harbour' => 'Departure Harbour',
            'approve'           => 'Approved',

            // Ring net
            'net_length' => 'Length of the Ring Net (m)',
            'net_height' => 'Height of the Net (m)',
            'fad'        => 'If fad is used mention',

            // Longline
            'mainline'    => 'Float Line Length (m)',
            'branchline'  => 'Branch Line Length (m)',
            'no_of_hooks' => 'Number of Hooks',
            'hook_type'   => 'Hook Type',
            'depth'       => 'Depth (m)',
            'bait'        => 'Bait Type',
            'no_hook_bet' => 'No of Hooks Between Float',

            // Gillnet
            'net_material' => 'Net Material',
            'mesh_size'    => 'Mesh Size (mm)',
            'ply'          => 'Ply of the Net',
            'set_depth'    => 'Depth at Which Net is Set (m)',
            'length'       => 'Length of the Net (m)',
            'net_pieces'   => 'Number of Net Pieces',
        ];
    }

    public static function gearTypeList()
    {
        return [
            self::GEAR_RINGNET  => 'Ring Net',
        ];
    }

    public static function hookTypeList()
    {
        return [
            self::HOOK_J36 => 'J 36',
            self::HOOK_J26 => 'J 26',
            self::HOOK_O83 => 'O 83',
            self::HOOK_O17 => 'O 17',
        ];
    }

    public static function baitList()
    {
        return [
            self::BAIT_SQUID      => 'Squid',
            self::BAIT_FLYINGFISH => 'Flying Fish',
            self::BAIT_MILKFISH   => 'Milkfish',
            self::BAIT_INDIANSCAD => 'Indian Scad',
            self::BAIT_OTHER      => 'Other',
        ];
    }

    public static function materialList()
    {
        return [
            self::MAT_NYLON_BRAIDED       => 'Nylon Braided',
            self::MAT_NYLON_MULTIFILAMENT => 'Nylon Multifilament',
            self::MAT_OTHER               => 'Other',
        ];
    }

    public function getVessel()
    {
        return $this->hasOne(BoatNumbers::class, ['id' => 'vessel_id']);
    }

    public function getArrivalHarbour()
    {
        return $this->hasOne(MHarbours::class, ['Id' => 'arrival_harbour']);
    }

    public function getDepartureHarbour()
    {
        return $this->hasOne(MHarbours::class, ['Id' => 'departure_harbour']);
    }
}