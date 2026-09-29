<?php

namespace backend\models;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "highseas_license".
 *
 * @property int $id
 * @property string|null $license_number
 * @property int $fisherman_id
 * @property int $boat_registration_id
 * @property int $skipper_id
 * @property string $prevouse_boat_flag
 * @property int $no_if_crew_members
 * @property int $main_gear_type
 * @property int|null $fishing_gear_type
 * @property int $district
 * @property int $division
 * @property int|null $landing_harbour
 * @property string|null $unloading_sites
 * @property int $status
 * @property string $approval_stage
 * @property string $created
 * @property string|null $approved_time
 * @property string|null $expire_date
 * @property int $renew
 *
 * @property FishermanRegisterdBoat $boatRegistration
 * @property MFiDistrict $district0
 * @property ProfileFisherman $fisherman
 * @property DistrictGearTypes $fishingGearType
 * @property MHarbours $landingHarbour
 * @property MMainGearTypes $mainGearType
 * @property Skipper $skipper
 */
class HighseasLicense extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'highseas_license';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fisherman_id', 'boat_registration_id', 'skipper_id', 'district', 'division', 'approval_stage', 'fishing_gear_type'], 'required'],
            [['fisherman_id', 'boat_registration_id', 'skipper_id', 'no_if_crew_members', 'main_gear_type', 'district', 'division', 'landing_harbour', 'status', 'renew'], 'integer'],
            [['created', 'approved_time', 'expire_date', 'unloading_sites', 'prevouse_boat_flag', 'no_if_crew_members'], 'safe'],
            [['license_number', 'approval_stage', 'fishing_gear_type'], 'string', 'max' => 100],
            [['prevouse_boat_flag'], 'string', 'max' => 11],
            [['boat_registration_id'], 'exist', 'skipOnError' => true, 'targetClass' => FishermanRegisterdBoat::class, 'targetAttribute' => ['boat_registration_id' => 'id']],
            [['district'], 'exist', 'skipOnError' => true, 'targetClass' => MFiDistrict::class, 'targetAttribute' => ['district' => 'id']],
            [['fisherman_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProfileFisherman::class, 'targetAttribute' => ['fisherman_id' => 'id']],
            [['fishing_gear_type'], 'exist', 'skipOnError' => true, 'targetClass' => DistrictGearTypes::class, 'targetAttribute' => ['fishing_gear_type' => 'id']],
            [['landing_harbour'], 'exist', 'skipOnError' => true, 'targetClass' => MHarbours::class, 'targetAttribute' => ['landing_harbour' => 'id']],
            [['main_gear_type'], 'exist', 'skipOnError' => true, 'targetClass' => MMainGearTypes::class, 'targetAttribute' => ['main_gear_type' => 'id']],
            [['skipper_id'], 'exist', 'skipOnError' => true, 'targetClass' => Skipper::class, 'targetAttribute' => ['skipper_id' => 'id']],
            [['prevouse_boat_flag', 'no_if_crew_members'], 'required', 'when' => function ($model) {
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
            'license_number' => Yii::t('app', 'License Number'),
            'fisherman_id' => Yii::t('app', 'Fisherman ID'),
            'boat_registration_id' => Yii::t('app', 'Boat Registration ID'),
            'skipper_id' => Yii::t('app', 'Skipper ID'),
            'prevouse_boat_flag' => Yii::t('app', 'Previous Boat Flag'),
            'no_if_crew_members' => Yii::t('app', 'No If Crew Members'),
            'main_gear_type' => Yii::t('app', 'Main Gear Type'),
            'fishing_gear_type' => Yii::t('app', 'Fishing Gear Type'),
            'district' => Yii::t('app', 'District'),
            'division' => Yii::t('app', 'Division'),
            'landing_harbour' => Yii::t('app', 'Landing Harbour'),
            'unloading_sites' => Yii::t('app', 'Unloading Sites'),
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
     * Gets query for [[District0]].
     *
     * @return ActiveQuery
     */
    public function getDistrict0()
    {
        return $this->hasOne(MFiDistrict::class, ['id' => 'district']);
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
     * Gets query for [[FishingGearType]].
     *
     * @return ActiveQuery
     */
    public function getFishingGearType()
    {
        return $this->hasOne(DistrictGearTypes::class, ['id' => 'fishing_gear_type']);
    }

    /**
     * Gets query for [[LandingHarbour]].
     *
     * @return ActiveQuery
     */
    public function getLandingHarbour()
    {
        return $this->hasOne(MHarbours::class, ['id' => 'landing_harbour']);
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

    /**
     * Gets query for [[Skipper]].
     *
     * @return ActiveQuery
     */
    public function getSkipper()
    {
        return $this->hasOne(Skipper::class, ['id' => 'skipper_id']);
    }
}
