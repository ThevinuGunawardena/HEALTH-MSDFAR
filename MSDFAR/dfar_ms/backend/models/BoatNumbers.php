<?php

namespace backend\models;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "boat_numbers".
 *
 * @property int $id
 * @property int|null $yard
 * @property int|null $boat_design
 * @property int|null $owner
 * @property int|null $boat_type
 * @property string|null $boat_number
 * @property int|null $fisheries_district
 * @property int|null $fisheries_division
 * @property string|null $remarks
 * @property int|null $status
 * @property string|null $approval_stage
 * @property string|null $created
 * @property string|null $approved_time
 * @property string|null $expire_date
 *
 * @property BoatDesign $boatDesign
 * @property MBoatTypes $boatType
 * @property MFiDistrict $fisheriesDistrict
 * @property MDivision $fisheriesDivision
 * @property FishermanRegisterdBoat[] $fishermanRegisterdBoats
 * @property ProfileFisherman $owner0
 * @property ProfileYard $yard0
 */
class BoatNumbers extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'boat_numbers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['yard', 'boat_design', 'owner', 'boat_type', 'fisheries_district', 'fisheries_division', 'status'], 'integer'],
            [['yard', 'boat_design', 'owner', 'boat_type', "hull_number"], 'required'],
            [['created', 'approved_time', 'expire_date'], 'safe'],
            [['boat_number'], 'string', 'max' => 100],
            [['length'], 'number'],
            [['remarks'], 'string', 'max' => 300],
            [['additional_conditions'], 'string', 'max' => 500],
            [['approval_stage'], 'string', 'max' => 50],
            [['fisheries_district'], 'exist', 'skipOnError' => true, 'targetClass' => MFiDistrict::class, 'targetAttribute' => ['fisheries_district' => 'id']],
            [['boat_type'], 'exist', 'skipOnError' => true, 'targetClass' => MBoatTypes::class, 'targetAttribute' => ['boat_type' => 'id']],
            [['boat_design'], 'exist', 'skipOnError' => true, 'targetClass' => BoatDesign::class, 'targetAttribute' => ['boat_design' => 'id']],
            [['fisheries_division'], 'exist', 'skipOnError' => true, 'targetClass' => MDivision::class, 'targetAttribute' => ['fisheries_division' => 'id']],
            [['owner'], 'exist', 'skipOnError' => true, 'targetClass' => ProfileFisherman::class, 'targetAttribute' => ['owner' => 'id']],
            [['yard'], 'exist', 'skipOnError' => true, 'targetClass' => ProfileYard::class, 'targetAttribute' => ['yard' => 'id']],
            ['additional_conditions', 'required', 'when' => function ($model) {
                return UserTypeUtil::hasType(Constant::MANAGEMENT);
            }, 'enableClientValidation' => false],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Ref number'),
            'yard' => Yii::t('app', 'Yard'),
            'boat_design' => Yii::t('app', 'Boat Design'),
            'owner' => Yii::t('app', 'Owner'),
            'boat_type' => Yii::t('app', 'Boat Type'),
            'boat_number' => Yii::t('app', 'Boat Number'),
            'fisheries_district' => Yii::t('app', 'Fisheries District'),
            'fisheries_division' => Yii::t('app', 'Fisheries Division'),
            'remarks' => Yii::t('app', 'Remarks'),
            'status' => Yii::t('app', 'Status'),
            'approval_stage' => Yii::t('app', 'Approval Stage'),
            'created' => Yii::t('app', 'Created'),
            'approved_time' => Yii::t('app', 'Approved Time'),
            'expire_date' => Yii::t('app', 'Expire Date'),
        ];
    }

    /**
     * Gets query for [[BoatDesign]].
     *
     * @return ActiveQuery
     */
    public function getBoatDesign()
    {
        return $this->hasOne(BoatDesign::class, ['id' => 'boat_design']);
    }

    /**
     * Gets query for [[BoatType]].
     *
     * @return ActiveQuery
     */
    public function getBoatType()
    {
        return $this->hasOne(MBoatTypes::class, ['id' => 'boat_type']);
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
     * Gets query for [[FisheriesDivision]].
     *
     * @return ActiveQuery
     */
    public function getFisheriesDivision()
    {
        return $this->hasOne(MDivision::class, ['id' => 'fisheries_division']);
    }

    /**
     * Gets query for [[FishermanRegisterdBoats]].
     *
     * @return ActiveQuery
     */
    public function getFishermanRegisterdBoats()
    {
        return $this->hasMany(FishermanRegisterdBoat::class, ['boat_number_id' => 'id']);
    }

    /**
     * Gets query for [[Owner0]].
     *
     * @return ActiveQuery
     */
    public function getOwner0()
    {
        return $this->hasOne(ProfileFisherman::class, ['id' => 'owner']);
    }

    /**
     * Gets query for [[Yard0]].
     *
     * @return ActiveQuery
     */
    public function getYard0()
    {
        return $this->hasOne(ProfileYard::class, ['id' => 'yard']);
    }

    public function getFishermanRegisterdBoatLicenses()
{
    return $this->hasMany(
        FishermanRegisterdBoatLicense::class,
        ['boat_number_id' => 'id']
    );
}
}
