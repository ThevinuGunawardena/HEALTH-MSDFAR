<?php

namespace backend\models;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "national_license".
 *
 * @property int $id
 * @property int $fisherman_id
 * @property int $boat_registration_id
 * @property string|null $license_number
 * @property int $fisheries_district
 * @property int $division
 * @property string|null $division_gear_types
 * @property int $main_gear_type
 * @property int|null $landing_site
 * @property int $status
 * @property string $approval_stage
 * @property string $created
 * @property string|null $approved_time
 * @property string|null $expire_date
 * @property int $renew
 *
 * @property FishermanRegisterdBoat $boatRegistration
 * @property MDivision $division0
 * @property MFiDistrict $fisheriesDistrict
 * @property ProfileFisherman $fisherman
 * @property MLandingSite $landingSite
 * @property MMainGearTypes $mainGearType
 */
class NationalLicense extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'national_license';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fisherman_id', 'boat_registration_id', 'fisheries_district', 'division', 'approval_stage'], 'required'],
            [['fisherman_id', 'boat_registration_id', 'fisheries_district', 'division', 'main_gear_type', 'landing_site', 'status', 'renew'], 'integer'],
            [['created', 'approved_time', 'expire_date', 'division_gear_types', 'main_gear_type'], 'safe'],
            [['license_number'], 'string', 'max' => 100],
            [['approval_stage'], 'string', 'max' => 11],
            [['division'], 'exist', 'skipOnError' => true, 'targetClass' => MDivision::class, 'targetAttribute' => ['division' => 'id']],
            [['fisheries_district'], 'exist', 'skipOnError' => true, 'targetClass' => MFiDistrict::class, 'targetAttribute' => ['fisheries_district' => 'id']],
            [['fisherman_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProfileFisherman::class, 'targetAttribute' => ['fisherman_id' => 'id']],
            [['landing_site'], 'exist', 'skipOnError' => true, 'targetClass' => MLandingSite::class, 'targetAttribute' => ['landing_site' => 'id']],
            [['boat_registration_id'], 'exist', 'skipOnError' => true, 'targetClass' => FishermanRegisterdBoat::class, 'targetAttribute' => ['boat_registration_id' => 'id']],
            ['division_gear_types', 'required', 'when' => function ($model) {
                return UserTypeUtil::hasType(Constant::FISHERMAN) || UserTypeUtil::hasType(Constant::FI);
            }, 'enableClientValidation' => false],
            ['landing_site', 'required', 'when' => function ($model) {
                return UserTypeUtil::hasType(Constant::FI);
            }, 'enableClientValidation' => false],


        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'fisherman_id' => Yii::t('app', 'Fisherman ID'),
            'boat_registration_id' => Yii::t('app', 'Boat Registration ID'),
            'license_number' => Yii::t('app', 'License Number'),
            'fisheries_district' => Yii::t('app', 'Fisheries District'),
            'division' => Yii::t('app', 'Division'),
            'division_gear_types' => Yii::t('app', 'Division Gear Types'),
            'main_gear_type' => Yii::t('app', 'Main Gear Type'),
            'landing_site' => Yii::t('app', 'Landing Site'),
            'status' => Yii::t('app', 'Status'),
            'approval_stage' => Yii::t('app', 'Approval Stage'),
            'created' => Yii::t('app', 'Created'),
            'approved_time' => Yii::t('app', 'Approved Time'),
            'expire_date' => Yii::t('app', 'Expire Date'),
            'renew' => Yii::t('app', 'Renew'),
        ];
    }

    /**
     * Gets query for [[BoatRegistration]].
     *
     * @return ActiveQuery
     */
    public function getBoatRegistration()
    {
        return $this->hasOne(FishermanRegisterdBoat::class, ['id' => 'boat_registration_id']);
    }

    /**
     * Gets query for [[Division0]].
     *
     * @return ActiveQuery
     */
    public function getDivision0()
    {
        return $this->hasOne(MDivision::class, ['id' => 'division']);
    }

    /**
     * Gets query for [[FisheriesDistrict]].
     *
     * @return ActiveQuery
     */
    public function getFisheriesDistrict()
    {
        return $this->hasOne(MFiDistrict::class, ['id' => 'fisheries_district']);
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

    /**
     * Gets query for [[MainGearType]].
     *
     * @return ActiveQuery
     */
    public function getMainGearType()
    {
        return $this->hasOne(MMainGearTypes::class, ['id' => 'main_gear_type']);
    }
}
