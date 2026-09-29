<?php

namespace backend\models;

use yii\db\ActiveRecord;

class ELogGillnet extends ActiveRecord
{
    // Net material constants
    const MAT_NYLON_BRAIDED       = 'nylon_braided';
    const MAT_NYLON_MULTIFILAMENT = 'nylon_multifilament';
    const MAT_OTHER               = 'other';

    public static function tableName()
    {
        return 'e_log_gillnet';
    }

    public function rules()
    {
        return [
            // all fields required
            [['e_log_id',  'mesh_size',
               'set_depth' ,'length'], 'required'],

            // integers
            [['e_log_id', 'ply', 'net_pieces'], 'integer'],

            // decimals
            [['mesh_size', 'net_height', 'set_depth', 'length'], 'number'],

            // only allow defined material options
            ['net_material', 'in', 'range' => array_keys(self::materialList())],
        ];
    }

    public function attributeLabels()
    {
        return [
            'e_log_id'     => 'E-Log Reference',
            'net_material' => 'Net Material',
            'mesh_size'    => 'Mesh Size (mm)',
            'ply'          => 'Ply of the Net',
            'net_height'   => 'Height of the Net (m)',
            'set_depth'    => 'Depth at Which Net is Set (m)',
            'length'       => 'Length of the Net (m)',
            'net_pieces'   => 'Number of Net Pieces',
        ];
    }

    // Dropdown list for net materials
    public static function materialList()
    {
        return [
            self::MAT_NYLON_BRAIDED       => 'Nylon Braided',
            self::MAT_NYLON_MULTIFILAMENT => 'Nylon Multifilament',
            self::MAT_OTHER               => 'Other',
        ];
    }

    // Relationship back to parent ELog
    public function getELog()
    {
        return $this->hasOne(ELog::class, ['id' => 'e_log_id']);
    }
}