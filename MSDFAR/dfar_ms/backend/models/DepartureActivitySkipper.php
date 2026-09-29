<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "departure_activity_skipper".
 *
 * @property int $id
 * @property string $skipper_id
 * @property string $nic
 * @property string $served_vessel
 * @property string $dep_date
 * @property string $dep_id
 * @property string $activity
 * @property string $description
 * @property string $date_time
 * @property string $to_date
 * @property string $user_name
 */
class DepartureActivitySkipper extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'departure_activity_skipper';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['skipper_id', 'nic', 'served_vessel', 'dep_id', 'activity', 'description', 'date_time', 'to_date', 'user_name'], 'required'],
            [['dep_date', 'date_time'], 'safe'],
            [['to_date'], 'string'],
            [['skipper_id', 'served_vessel'], 'string', 'max' => 25],
            [['nic', 'dep_id', 'user_name'], 'string', 'max' => 20],
            [['activity', 'description'], 'string', 'max' => 225],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'skipper_id' => Yii::t('app', 'Skipper ID'),
            'nic' => Yii::t('app', 'Nic'),
            'served_vessel' => Yii::t('app', 'Served Vessel'),
            'dep_date' => Yii::t('app', 'Dep Date'),
            'dep_id' => Yii::t('app', 'Dep ID'),
            'activity' => Yii::t('app', 'Activity'),
            'description' => Yii::t('app', 'Description'),
            'date_time' => Yii::t('app', 'Date Time'),
            'to_date' => Yii::t('app', 'To Date'),
            'user_name' => Yii::t('app', 'User Name'),
        ];
    }
}
