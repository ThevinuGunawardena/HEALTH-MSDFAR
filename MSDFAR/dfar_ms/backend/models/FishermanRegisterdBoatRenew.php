<?php

namespace backend\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "fisherman_registerd_boat_renew".
 *
 * @property int $id
 * @property int $boat_number_id
 * @property int $fisherman_id
 * @property string|null $insurance_no
 * @property string|null $call_sign_no
 * @property int $district
 * @property int $division
 * @property int|null $landing_site
 * @property string|null $witness_name
 * @property string|null $witness_address
 * @property string|null $witness_nic
 * @property string|null $witness_singing_date
 * @property string|null $how_propelled
 * @property string|null $engine_make
 * @property string|null $fuel_type
 * @property string|null $engine_type
 * @property float|null $engine_horsepower
 * @property string|null $engine_serial_number
 * @property string|null $communication_equipment
 * @property string|null $fishing_equipment
 * @property string|null $navigation_equipment
 * @property string|null $date_of_construction
 * @property string|null $date_of_first_registration
 * @property string|null $mea_report
 * @property int $status
 * @property string $approval_stage
 * @property string $created
 * @property string|null $approved_time
 * @property string|null $expire_date
 * @property int $renewal
 *
 * @property BoatNumbers $boatNumber
 * @property ProfileFisherman $fisherman
 * @property MLandingSite $landingSite
 *  *  * @property MFiDistrict $fisheriesDistrict

 */
class FishermanRegisterdBoatRenew extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fisherman_registerd_boat_renew';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['boat_number_id', 'fisherman_id', 'district', 'division', 'approval_stage'], 'required'],
            [['boat_number_id', 'fisherman_id', 'district', 'division', 'landing_site', 'status', 'renewal'], 'integer'],
            [['witness_singing_date', 'date_of_construction', 'date_of_first_registration', 'created', 'approved_time', 'expire_date'], 'safe'],
//            [['engine_horsepower'], 'number'],
            [['insurance_no', 'call_sign_no', 'engine_type', 'engine_serial_number', 'mea_report'], 'string', 'max' => 100],
            [['witness_name', 'witness_address', 'communication_equipment', 'fishing_equipment', 'navigation_equipment', 'approval_stage'], 'string', 'max' => 200],
            [['witness_nic', 'how_propelled', 'fuel_type', 'engine_horsepower'], 'string', 'max' => 50],
            [['engine_make'], 'string', 'max' => 11],
            [['boat_number_id'], 'exist', 'skipOnError' => true, 'targetClass' => BoatNumbers::class, 'targetAttribute' => ['boat_number_id' => 'id']],
            [['fisherman_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProfileFisherman::class, 'targetAttribute' => ['fisherman_id' => 'id']],
            [['landing_site'], 'exist', 'skipOnError' => true, 'targetClass' => MLandingSite::class, 'targetAttribute' => ['landing_site' => 'id']],
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
            'fisherman_id' => Yii::t('app', 'Fisherman ID'),
            'insurance_no' => Yii::t('app', 'Insurance No'),
            'call_sign_no' => Yii::t('app', 'Call Sign No'),
            'district' => Yii::t('app', 'District'),
            'division' => Yii::t('app', 'Division'),
            'landing_site' => Yii::t('app', 'Landing Site'),
            'witness_name' => Yii::t('app', 'Witness Name'),
            'witness_address' => Yii::t('app', 'Witness Address'),
            'witness_nic' => Yii::t('app', 'Witness Nic'),
            'witness_singing_date' => Yii::t('app', 'Witness Singing Date'),
            'how_propelled' => Yii::t('app', 'How Propelled'),
            'engine_make' => Yii::t('app', 'Engine Make'),
            'fuel_type' => Yii::t('app', 'Fuel Type'),
            'engine_type' => Yii::t('app', 'Engine Type'),
            'engine_horsepower' => Yii::t('app', 'Engine Horsepower'),
            'engine_serial_number' => Yii::t('app', 'Engine Serial Number'),
            'communication_equipment' => Yii::t('app', 'Communication Equipment'),
            'fishing_equipment' => Yii::t('app', 'Fishing Equipment'),
            'navigation_equipment' => Yii::t('app', 'Navigation Equipment'),
            'date_of_construction' => Yii::t('app', 'Date Of Construction'),
            'date_of_first_registration' => Yii::t('app', 'Date Of First Registration'),
            'mea_report' => Yii::t('app', 'Mea Report'),
            'status' => Yii::t('app', 'Status'),
            'approval_stage' => Yii::t('app', 'Approval Stage'),
            'created' => Yii::t('app', 'Created'),
            'approved_time' => Yii::t('app', 'Approved Time'),
            'expire_date' => Yii::t('app', 'Expire Date'),
            'renewal' => Yii::t('app', 'Renewal'),
        ];
    }

    /**
     * Gets query for [[BoatNumber]].
     *
     * @return ActiveQuery
     */
    public function getBoatNumber()
    {
        return $this->hasOne(BoatNumbers::class, ['id' => 'boat_number_id']);
    }

    /**
     * Gets query for [[Fisherman]].
     *
     * @return ActiveQuery
     */
    public function getFisherman()
    {
        return $this->hasOne(ProfileFisherman::class, ['id' => 'fisherman_id']);
    }

    /**
     * Gets query for [[LandingSite]].
     *
     * @return ActiveQuery
     */
    public function getLandingSite()
    {
        return $this->hasOne(MLandingSite::class, ['id' => 'landing_site']);
    }


    public function getFisheriesDistrict()
    {
        return $this->hasOne(MFiDistrict::class, ['id' => 'district']);
    }

    /**
     * Gets query for [[FisheriesDivision]].
     *
     * @return ActiveQuery
     */
    public function getFisheriesDivision()
    {
        return $this->hasOne(MDivision::class, ['id' => 'division']);
    }
}
