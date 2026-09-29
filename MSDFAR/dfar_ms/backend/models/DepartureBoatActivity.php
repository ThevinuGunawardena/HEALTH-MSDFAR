<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "departure_boat_activity".
 *
 * @property int $id
 * @property string $boat_no
 * @property string $activity
 * @property string $description
 * @property string $date_time
 * @property string $to_date
 * @property string $user_name
 */
class DepartureBoatActivity extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'departure_boat_activity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['boat_no', 'activity', 'description', 'date_time', 'to_date', 'user_name'], 'required'],
            [['date_time'], 'safe'],
            [['to_date'], 'string'],
            [['boat_no', 'activity', 'description'], 'string', 'max' => 225],
            [['user_name'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'boat_no' => Yii::t('app', 'Boat No'),
            'activity' => Yii::t('app', 'Activity'),
            'description' => Yii::t('app', 'Description'),
            'date_time' => Yii::t('app', 'Date Time'),
            'to_date' => Yii::t('app', 'To Date'),
            'user_name' => Yii::t('app', 'User Name'),
        ];
    }
}
