<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "fisrstregwithboatis".
 *
 * @property int $id
 * @property int|null $boat_number_id
 * @property string|null $boat_number
 * @property string|null $date_of_first_registration
 * @property string|null $last_modified_time
 */
class Fisrstregwithboatis extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fisrstregwithboatis';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'boat_number_id'], 'integer'],
            [['date_of_first_registration', 'last_modified_time'], 'safe'],
            [['boat_number'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'boat_number_id' => Yii::t('app', 'Boat Number ID'),
            'boat_number' => Yii::t('app', 'Boat Number'),
            'date_of_first_registration' => Yii::t('app', 'Date Of First Registration'),
            'last_modified_time' => Yii::t('app', 'Last Modified Time'),
        ];
    }
}
