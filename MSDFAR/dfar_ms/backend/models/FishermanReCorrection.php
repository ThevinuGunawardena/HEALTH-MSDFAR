<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "fisherman_re_correction".
 *
 * @property int $id
 * @property int $fisherman_id
 * @property int $main_id
 * @property string $nic
 * @property string|null $division
 * @property string|null $district
 * @property string $updated_at
 */
class FishermanReCorrection extends ActiveRecord
{
    public static function tableName()
    {
        return 'fisherman_re_correction';
    }


    public function rules()
    {
        return [
            [['fisherman_id', 'main_id'], 'required'],
            [['fisherman_id', 'main_id'], 'integer'],
            [['nic'], 'string', 'max' => 20],
            [['division', 'district'], 'integer'],
            [['updated_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fisherman_id' => 'Fisherman ID',
            'main_id' => 'Main ID',
            'nic' => 'NIC',
            'division' => 'Division',
            'district' => 'District',
            'updated_at' => 'Updated At',
        ];
    }
}