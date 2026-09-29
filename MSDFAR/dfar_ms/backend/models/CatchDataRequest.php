<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "catch_data_request".
 *
 * @property int $id
 * @property string|null $landing_date
 * @property int|null $boat_registration_id
 * @property int|null $unloading_harbour
 * @property int|null $fishing_gear_type
 * @property string|null $log_book_no
 * @property string|null $log_book_page_no
 * @property string|null $created_at
 * @property int $created_by
 */
class CatchDataRequest extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'catch_data_request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['landing_date', 'boat_registration_id', 'unloading_harbour', 'fishing_gear_type', 'log_book_no', 'log_book_page_no', 'created_at'], 'default', 'value' => null],
            [['landing_date', 'created_at'], 'safe'],
            [['boat_registration_id', 'unloading_harbour', 'fishing_gear_type', 'created_by'], 'integer'],
            [['created_by','landing_date', 'boat_registration_id', 'unloading_harbour', 'fishing_gear_type', 'log_book_no', 'log_book_page_no'], 'required'],
            [['log_book_no', 'log_book_page_no'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'landing_date' => Yii::t('app', 'Landing Date'),
            'boat_registration_id' => Yii::t('app', 'Boat Registration ID'),
            'unloading_harbour' => Yii::t('app', 'Unloading Harbour'),
            'fishing_gear_type' => Yii::t('app', 'Fishing Gear Type'),
            'log_book_no' => Yii::t('app', 'Log Book No'),
            'log_book_page_no' => Yii::t('app', 'Log Book Page No'),
            'created_at' => Yii::t('app', 'Created At'),
            'created_by' => Yii::t('app', 'Created By'),
        ];
    }

}
