<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "scientific_sampling_boat_gear_data".
 *
 * @property int $id
 * @property int $sampling_data_id
 * @property int $fishey_type
 * @property int|null $sub_category
 * @property string $engine_hp
 * @property string $departure_date
 * @property string $departure_time
 * @property int $departure_district
 * @property int $departure_division
 * @property int $depature_port
 * @property string $weather
 * @property string $arrival_date
 * @property string $remark
 * @property int $crew_members_count
 * @property string $unloading_type
 * @property string $gear_setting_time
 * @property int $days
 * @property int $hours
 * @property int $status
 *
 * @property MFiDistrict $departureDistrict
 * @property MDivision $departureDivision
 * @property MLandingSite $depaturePort
 * @property MFisheryTypes $fisheyType
 * @property ScientificSamplingData $samplingData
 * @property ScientificSamplingGearData[] $scientificSamplingGearDatas
 * @property MBoatCategory $subCategory
 * @property MBoatCategory $subCategory0
 */
class ScientificSamplingBoatGearData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'scientific_sampling_boat_gear_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sampling_data_id', 'fishey_type', 'engine_hp', 'departure_date', 'departure_time', 'departure_district', 'departure_division', 'depature_port', 'weather', 'arrival_date',  'crew_members_count', 'unloading_type', 'gear_setting_time', 'days', 'hours'], 'required'],
            [['sampling_data_id', 'fishey_type', 'sub_category', 'departure_district', 'departure_division', 'depature_port', 'crew_members_count', 'days', 'hours', 'status'], 'integer'],
            [['departure_date', 'departure_time', 'arrival_date'], 'safe'],
            [['engine_hp'], 'string', 'max' => 11],
            [['weather', 'remark', 'unloading_type'], 'string', 'max' => 200],
            [['gear_setting_time'], 'string', 'max' => 100],
            [['sub_category'], 'exist', 'skipOnError' => true, 'targetClass' => MBoatCategory::class, 'targetAttribute' => ['sub_category' => 'id']],
            [['departure_district'], 'exist', 'skipOnError' => true, 'targetClass' => MFiDistrict::class, 'targetAttribute' => ['departure_district' => 'id']],
            [['departure_division'], 'exist', 'skipOnError' => true, 'targetClass' => MDivision::class, 'targetAttribute' => ['departure_division' => 'id']],
            [['fishey_type'], 'exist', 'skipOnError' => true, 'targetClass' => MFisheryTypes::class, 'targetAttribute' => ['fishey_type' => 'id']],
            [['depature_port'], 'exist', 'skipOnError' => true, 'targetClass' => MLandingSite::class, 'targetAttribute' => ['depature_port' => 'id']],
            [['sampling_data_id'], 'exist', 'skipOnError' => true, 'targetClass' => ScientificSamplingData::class, 'targetAttribute' => ['sampling_data_id' => 'id']],
            [['sub_category'], 'exist', 'skipOnError' => true, 'targetClass' => MBoatCategory::class, 'targetAttribute' => ['sub_category' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'sampling_data_id' => Yii::t('app', 'Sampling Data ID'),
            'fishey_type' => Yii::t('app', 'Fishey Type'),
            'sub_category' => Yii::t('app', 'Sub Category'),
            'engine_hp' => Yii::t('app', 'Engine Hp'),
            'departure_date' => Yii::t('app', 'Departure Date'),
            'departure_time' => Yii::t('app', 'Departure Time'),
            'departure_district' => Yii::t('app', 'Departure District'),
            'departure_division' => Yii::t('app', 'Departure Division'),
            'depature_port' => Yii::t('app', 'Depature Port'),
            'weather' => Yii::t('app', 'Weather'),
            'arrival_date' => Yii::t('app', 'Arrival Date'),
            'remark' => Yii::t('app', 'Remark'),
            'crew_members_count' => Yii::t('app', 'Crew Members Count'),
            'unloading_type' => Yii::t('app', 'Unloading Type'),
            'gear_setting_time' => Yii::t('app', 'Gear Setting Time'),
            'days' => Yii::t('app', 'Days'),
            'hours' => Yii::t('app', 'Hours'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Gets query for [[DepartureDistrict]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepartureDistrict()
    {
        return $this->hasOne(MFiDistrict::class, ['id' => 'departure_district']);
    }

    /**
     * Gets query for [[DepartureDivision]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepartureDivision()
    {
        return $this->hasOne(MDivision::class, ['id' => 'departure_division']);
    }

    /**
     * Gets query for [[DepaturePort]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepaturePort()
    {
        return $this->hasOne(MLandingSite::class, ['id' => 'depature_port']);
    }

    /**
     * Gets query for [[FisheyType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFisheyType()
    {
        return $this->hasOne(MFisheryTypes::class, ['id' => 'fishey_type']);
    }

    /**
     * Gets query for [[SamplingData]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSamplingData()
    {
        return $this->hasOne(ScientificSamplingData::class, ['id' => 'sampling_data_id']);
    }

    /**
     * Gets query for [[ScientificSamplingGearDatas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScientificSamplingGearDatas()
    {
        return $this->hasMany(ScientificSamplingGearData::class, ['boat_data_id' => 'id']);
    }

    /**
     * Gets query for [[SubCategory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubCategory()
    {
        return $this->hasOne(MBoatCategory::class, ['id' => 'sub_category']);
    }

    /**
     * Gets query for [[SubCategory0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubCategory0()
    {
        return $this->hasOne(MBoatCategory::class, ['id' => 'sub_category']);
    }
}
