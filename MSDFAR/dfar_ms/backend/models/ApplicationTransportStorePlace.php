<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "application_transport_store_place".
 *
 * @property int $id
 * @property int $application_id
 * @property string|null $species
 * @property int|null $weight_per_distict
 * @property string|null $purchasing_district
 * @property string|null $intermediat_destination
 * @property string|null $final_store_place
 * @property string|null $transport_method
 * @property string|null $vehicle_number
 * @property string|null $boat_number
 */
class ApplicationTransportStorePlace extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'application_transport_store_place';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['application_id'], 'required'],
            [['application_id', 'weight_per_distict'], 'integer'],
            [['species', 'purchasing_district', 'intermediat_destination', 'final_store_place', 'transport_method', 'vehicle_number', 'boat_number'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'application_id' => Yii::t('app', 'Application ID'),
            'species' => Yii::t('app', 'Species'),
            'weight_per_distict' => Yii::t('app', 'Weight Per Distict'),
            'purchasing_district' => Yii::t('app', 'Purchasing District'),
            'intermediat_destination' => Yii::t('app', 'Intermediat Destination'),
            'final_store_place' => Yii::t('app', 'Final Store Place'),
            'transport_method' => Yii::t('app', 'Transport Method'),
            'vehicle_number' => Yii::t('app', 'Vehicle Number'),
            'boat_number' => Yii::t('app', 'Boat Number'),
        ];
    }
}
