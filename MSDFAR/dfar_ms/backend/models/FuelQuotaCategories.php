<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "fuel_quota_categories".
 *
 * @property int $id
 * @property string $category
 * @property int $fuel_quota
 * @property int $time_frame
 * @property int $boat_type
 */
class FuelQuotaCategories extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fuel_quota_categories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category', 'fuel_quota', 'time_frame', 'boat_type'], 'required'],
            [['fuel_quota', 'time_frame', 'boat_type'], 'integer'],
            [['category'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'category' => Yii::t('app', 'Category'),
            'fuel_quota' => Yii::t('app', 'Fuel Quota'),
            'time_frame' => Yii::t('app', 'Time Frame'),
            'boat_type' => Yii::t('app', 'Boat Type'),
        ];
    }

}
