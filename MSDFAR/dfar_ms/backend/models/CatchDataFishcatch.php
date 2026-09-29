<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "catch_data_fishcatch".
 *
 * @property int $id
 * @property int $fish_type
 * @property int $num_of_fish_log
 * @property int $weight_of_fish_log
 * @property int $num_of_fish_act
 * @property int $weight_of_fish_act
 * @property int $remain_fish_count
 * @property int $remain_weight 
 * @property int $catchdata_req_id
 */
class CatchDataFishcatch extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'catch_data_fishcatch';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fish_type', 'num_of_fish_log', 'weight_of_fish_log', 'num_of_fish_act', 'weight_of_fish_act', 'catchdata_req_id','remain_fish_count','remain_weight'], 'required'],
            [['fish_type', 'num_of_fish_log', 'weight_of_fish_log', 'num_of_fish_act', 'weight_of_fish_act', 'catchdata_req_id','remain_fish_count','remain_weight'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'fish_type' => Yii::t('app', 'Fish Type'),
            'num_of_fish_log' => Yii::t('app', 'Total Number of Fish According to Log Book'),
            'weight_of_fish_log' => Yii::t('app', 'Total Catch According to Log Book (KG)'),
            'num_of_fish_act' => Yii::t('app', 'Actual Number of Fish'),
            'weight_of_fish_act' => Yii::t('app', 'Actual Weight Of Catch'),
            'catchdata_req_id' => Yii::t('app', 'Catchdata Req ID'),
        ];
    }

}
