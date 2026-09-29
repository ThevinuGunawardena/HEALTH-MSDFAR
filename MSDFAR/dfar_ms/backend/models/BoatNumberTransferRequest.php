<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "boat_number_transfer_request".
 *
 * @property int $id
 * @property int $boat_number
 * @property int $new_owner
 * @property int $new_landing_district
 * @property int $new_landing_site
 * @property string|null $witness_name
 * @property string|null $witness_address
 * @property string|null $witness_nic
 * @property string|null $witness_sign_date
 * @property string|null $remark
 * @property int $status
 * @property string $approval_stage
 * @property string $created
 * @property string|null $approved_time
 *
 * @property BoatNumbers $boatNumber
 * @property MFiDistrict $newLandingDistrict
 * @property MLandingSite $newLandingSite
 * @property ProfileFisherman $newOwner
 */
class BoatNumberTransferRequest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'boat_number_transfer_request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['boat_number', 'new_owner', 'approval_stage'], 'required'],
            [['boat_number', 'new_owner', 'new_landing_district', 'new_landing_site', 'status'], 'integer'],
            [['witness_sign_date', 'created', 'approved_time'], 'safe'],
            [['witness_name', 'witness_address', 'remark'], 'string', 'max' => 500],
            [['witness_nic'], 'string', 'max' => 20],
            [['approval_stage'], 'string', 'max' => 50],
            [['boat_number'], 'exist', 'skipOnError' => true, 'targetClass' => BoatNumbers::class, 'targetAttribute' => ['boat_number' => 'id']],
            [['new_owner'], 'exist', 'skipOnError' => true, 'targetClass' => ProfileFisherman::class, 'targetAttribute' => ['new_owner' => 'id']],
            [['new_landing_district'], 'exist', 'skipOnError' => true, 'targetClass' => MFiDistrict::class, 'targetAttribute' => ['new_landing_district' => 'id']],
            [['new_landing_site'], 'exist', 'skipOnError' => true, 'targetClass' => MLandingSite::class, 'targetAttribute' => ['new_landing_site' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'boat_number' => Yii::t('app', 'Boat Number'),
            'new_owner' => Yii::t('app', 'New Owner'),
            'new_landing_district' => Yii::t('app', 'New Landing District'),
            'new_landing_site' => Yii::t('app', 'New Landing Site'),
            'witness_name' => Yii::t('app', 'Witness Name'),
            'witness_address' => Yii::t('app', 'Witness Address'),
            'witness_nic' => Yii::t('app', 'Witness Nic'),
            'witness_sign_date' => Yii::t('app', 'Witness Sign Date'),
            'remark' => Yii::t('app', 'Remark'),
            'status' => Yii::t('app', 'Status'),
            'approval_stage' => Yii::t('app', 'Approval Stage'),
            'created' => Yii::t('app', 'Created'),
            'approved_time' => Yii::t('app', 'Approved Time'),
        ];
    }

    /**
     * Gets query for [[BoatNumber]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBoatNumber()
    {
        return $this->hasOne(BoatNumbers::class, ['id' => 'boat_number']);
    }

    /**
     * Gets query for [[NewLandingDistrict]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewLandingDistrict()
    {
        return $this->hasOne(MFiDistrict::class, ['id' => 'new_landing_district']);
    }

    /**
     * Gets query for [[NewLandingSite]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewLandingSite()
    {
        return $this->hasOne(MLandingSite::class, ['id' => 'new_landing_site']);
    }

    /**
     * Gets query for [[NewOwner]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewOwner()
    {
        return $this->hasOne(ProfileFisherman::class, ['id' => 'new_owner']);
    }
}
