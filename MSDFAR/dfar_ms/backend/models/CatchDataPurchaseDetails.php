<?php

namespace backend\models;
use backend\models\User;

use Yii;

/**
 * This is the model class for table "catch_data_purchase_details".
 *
 * @property int $id
 * @property int $fish_type
 * @property int $No_of_Fish
 * @property int $Weight_of_Fish
 * @property int $exporter_uid
 * @property int $catch_data_request_id
 */
class CatchDataPurchaseDetails extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'catch_data_purchase_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fish_type', 'No_of_Fish', 'Weight_of_Fish', 'exporter_uid', 'catch_data_request_id'], 'required'],
            [['fish_type', 'No_of_Fish', 'Weight_of_Fish', 'exporter_uid', 'catch_data_request_id'], 'integer'],
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
            'No_of_Fish' => Yii::t('app', 'No Of Fish'),
            'Weight_of_Fish' => Yii::t('app', 'Weight Of Fish'),
            'exporter_uid' => Yii::t('app', 'Exporter Uid'),
            'catch_data_request_id' => Yii::t('app', 'Catch Data Request ID'),
        ];
    }

    public function getUser()
{
    return $this->hasOne(User::class, ['id' => 'exporter_uid']);
}

}
