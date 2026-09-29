<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

class ELogLongline extends ActiveRecord
{
    // Hook type constants — easy to use anywhere in your code
    const HOOK_J36 = 'J36';
    const HOOK_J26 = 'J26';
    const HOOK_O83 = 'O83';
    const HOOK_O17 = 'O17';

    // Bait type constants
    const BAIT_SQUID      = 'squid';
    const BAIT_FLYINGFISH = 'flyingfish';
    const BAIT_MILKFISH   = 'milkfish';

    const BAIT_INDIANSCAD   = 'indianscad';
    const BAIT_OTHER      = 'other';

    public static function tableName()
    {
        return 'e_log_longline';
    }

    public function rules()
    {
        return [
            // required fields
            [['e_log_id', 'mainline', 'branchline', 'no_of_hooks',
             'bait','no_hook_bet'], 'required'],

            // numeric
            [['e_log_id', 'no_of_hooks'], 'integer'],
            [['mainline', 'branchline', 'depth'], 'number'],

            // dropdown validations — only allow the defined options
            ['hook_type', 'in', 'range' => array_keys(self::hookTypeList())],
            ['bait',      'in', 'range' => array_keys(self::baitList())],
        ];
    }

    public function attributeLabels()
    {
        return [
            'e_log_id'    => 'E-Log Reference',
            'mainline'    => 'Float Line Length(m)',
            'branchline'  => 'Branch Line Length (m)',
            'no_of_hooks' => 'Number of Hooks',
            'hook_type'   => 'Hook Type',
            'depth'       => 'Depth (m)',
            'bait'        => 'Bait Type',
            'no_hook_bet' => 'No of hooks between float'
        ];
    }

    // Returns array for dropdowns
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

    // Relationship back to the main ELog record
    public function getELog()
    {
        return $this->hasOne(ELog::class, ['id' => 'e_log_id']);
    }
}