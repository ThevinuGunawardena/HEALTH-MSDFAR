<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "profile_yard".
 *
 * @property int $id
 * @property string $name
 * @property int $owner
 * @property string $address
 * @property string $mobile_number
 * @property string $land_line
 * @property string $email
 * @property string $web
 * @property string $fax
 * @property string $business_reg_no
 * @property string|null $business_reg_date
 * @property string $land_owner
 * @property string $deed_number
 * @property string|null $ownership_get_date
 * @property float $land_area
 * @property float $land_area_under_roof
 * @property string $remark
 * @property int $admin_district
 * @property int $fisheries_district
 * @property int $division
 * @property string $gps_latitude
 * @property string $gps_longitude
 * @property int $transpotation_method
 * @property float $distance_rural_hospital
 * @property float $distance_district_hospital
 * @property float $distance_base_hospital
 * @property float $distance_teaching_hospital
 * @property float $distance_genaral_hospital
 * @property float $distance_fire_brigade
 * @property float $distance_police_station
 * @property int $status
 * @property string $approval_stage
 * @property string $created
 * @property string|null $approved_time
 * @property string|null $expire_date
 *
 * @property MFiDistrict $adminDistrict
 * @property BoatDesign[] $boatDesigns
 * @property MDivision $division0
 * @property MFiDistrict $fisheriesDistrict
 * @property ProfileFisherman $owner0
 */
class ProfileYard extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'profile_yard';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'owner', 'address', 'mobile_number', 'land_line', 'email', 'web', 'fax', 'business_reg_no', 'land_owner', 'deed_number', 'land_area', 'land_area_under_roof', 'remark', 'admin_district', 'fisheries_district', 'division', 'gps_latitude', 'gps_longitude', 'transpotation_method', 'distance_rural_hospital', 'distance_district_hospital', 'distance_base_hospital', 'distance_teaching_hospital', 'distance_genaral_hospital', 'distance_fire_brigade', 'distance_police_station', 'approval_stage'], 'required'],
            [['owner', 'admin_district', 'fisheries_district', 'division', 'transpotation_method', 'status'], 'integer'],
            [['business_reg_date', 'ownership_get_date', 'created', 'approved_time', 'expire_date'], 'safe'],
            [['land_area', 'land_area_under_roof', 'distance_rural_hospital', 'distance_district_hospital', 'distance_base_hospital', 'distance_teaching_hospital', 'distance_genaral_hospital', 'distance_fire_brigade', 'distance_police_station'], 'number'],
            [['name', 'email', 'web', 'business_reg_no', 'land_owner'], 'string', 'max' => 200],
            [['address', 'remark'], 'string', 'max' => 500],
            [['mobile_number', 'land_line', 'fax'], 'string', 'max' => 12],
            [['deed_number'], 'string', 'max' => 100],
            [['gps_latitude', 'gps_longitude', 'approval_stage'], 'string', 'max' => 11],
            [['admin_district'], 'exist', 'skipOnError' => true, 'targetClass' => MFiDistrict::class, 'targetAttribute' => ['admin_district' => 'id']],
            [['division'], 'exist', 'skipOnError' => true, 'targetClass' => MDivision::class, 'targetAttribute' => ['division' => 'id']],
            [['fisheries_district'], 'exist', 'skipOnError' => true, 'targetClass' => MFiDistrict::class, 'targetAttribute' => ['fisheries_district' => 'id']],
            [['owner'], 'exist', 'skipOnError' => true, 'targetClass' => ProfileFisherman::class, 'targetAttribute' => ['owner' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'owner' => Yii::t('app', 'Owner'),
            'address' => Yii::t('app', 'Address'),
            'mobile_number' => Yii::t('app', 'Mobile Number'),
            'land_line' => Yii::t('app', 'Land Line'),
            'email' => Yii::t('app', 'Email'),
            'web' => Yii::t('app', 'Web'),
            'fax' => Yii::t('app', 'Fax'),
            'business_reg_no' => Yii::t('app', 'Business Reg No'),
            'business_reg_date' => Yii::t('app', 'Business Reg Date'),
            'land_owner' => Yii::t('app', 'Land Owner'),
            'deed_number' => Yii::t('app', 'Deed Number'),
            'ownership_get_date' => Yii::t('app', 'Ownership Get Date'),
            'land_area' => Yii::t('app', 'Land Area'),
            'land_area_under_roof' => Yii::t('app', 'Land Area Under Roof'),
            'remark' => Yii::t('app', 'Remark'),
            'admin_district' => Yii::t('app', 'Admin District'),
            'fisheries_district' => Yii::t('app', 'Fisheries District'),
            'division' => Yii::t('app', 'Division'),
            'gps_latitude' => Yii::t('app', 'Gps Latitude'),
            'gps_longitude' => Yii::t('app', 'Gps Longitude'),
            'transpotation_method' => Yii::t('app', 'Transpotation Method'),
            'distance_rural_hospital' => Yii::t('app', 'Distance Rural Hospital'),
            'distance_district_hospital' => Yii::t('app', 'Distance District Hospital'),
            'distance_base_hospital' => Yii::t('app', 'Distance Base Hospital'),
            'distance_teaching_hospital' => Yii::t('app', 'Distance Teaching Hospital'),
            'distance_genaral_hospital' => Yii::t('app', 'Distance Genaral Hospital'),
            'distance_fire_brigade' => Yii::t('app', 'Distance Fire Brigade'),
            'distance_police_station' => Yii::t('app', 'Distance Police Station'),
            'status' => Yii::t('app', 'Status'),
            'approval_stage' => Yii::t('app', 'Approval Stage'),
            'created' => Yii::t('app', 'Created'),
            'approved_time' => Yii::t('app', 'Approved Time'),
            'expire_date' => Yii::t('app', 'Expire Date'),
        ];
    }

    /**
     * Gets query for [[AdminDistrict]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdminDistrict()
    {
        return $this->hasOne(MFiDistrict::class, ['id' => 'admin_district']);
    }

    /**
     * Gets query for [[BoatDesigns]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBoatDesigns()
    {
        return $this->hasMany(BoatDesign::class, ['yard' => 'id']);
    }

    /**
     * Gets query for [[Division0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDivision0()
    {
        return $this->hasOne(MDivision::class, ['id' => 'division']);
    }

    /**
     * Gets query for [[FisheriesDistrict]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFisheriesDistrict()
    {
        return $this->hasOne(MFiDistrict::class, ['id' => 'fisheries_district']);
    }

    /**
     * Gets query for [[Owner0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOwner0()
    {
        return $this->hasOne(ProfileFisherman::class, ['id' => 'owner']);
    }
}
