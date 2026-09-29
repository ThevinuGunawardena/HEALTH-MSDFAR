<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "boat_number_owners_log".
 *
 * @property int $id
 * @property string $name
 * @property string $nic
 * @property string $from_date
 * @property string $to_date
 * @property int $status
 * @property int $added_by
 * @property string $created_time
 * @property string $boat_number
 */
class BoatNumberOwnersLog extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'boat_number_owners_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'nic', 'from_date', 'added_by', 'boat_number'], 'safe'],
            [['from_date', 'to_date', 'created_time'], 'safe'],
            [['status', 'added_by'], 'integer'],
            [['name'], 'string', 'max' => 500],
            [['nic'], 'string', 'max' => 50],
            [['boat_number'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'nic' => Yii::t('app', 'Nic'),
            'from_date' => Yii::t('app', 'From Date'),
            'to_date' => Yii::t('app', 'To Date'),
            'status' => Yii::t('app', 'Status'),
            'added_by' => Yii::t('app', 'Added By'),
            'created_time' => Yii::t('app', 'Created Time'),
            'boat_number' => Yii::t('app', 'Boat Number'),
        ];
    }
}
