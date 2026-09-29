<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "fisherman_registerd_boat".
 *
 * @property int $id
 * @property int $boat_number_id
 * @property int $fisherman_id
 * @property string|null $insurance_no
 * @property string|null $call_sign_no
 * @property int|null $landing_site
 * @property string|null $witness_name
 * @property string|null $witness_address
 * @property string|null $witness_nic
 * @property string|null $witness_singing_date
 * @property string|null $engine_make
 * @property float|null $engine_horsepower
 * @property string|null $engine_serial_number
 * @property string|null $communication_equipment
 * @property string|null $fishing_equipment
 * @property string|null $navigation_equipment
 * @property int|null $mea_report
 * @property int $status
 * @property string $approval_stage
 *
 * @property BoatNumbers $boatNumber
 * @property ProfileFisherman $fisherman
 * @property MLandingSite $landingSite
 * @property NationalLicense[] $nationalLicenses
 */
class FishermanRegisterdBoatFI extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fisherman_registerd_boat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['boat_number_id', 'fisherman_id', 'approval_stage'], 'required'],
            [['boat_number_id', 'fisherman_id', 'landing_site', 'mea_report', 'status'], 'integer'],
            [['witness_singing_date'], 'safe'],
            [['engine_horsepower'], 'number'],
            [['insurance_no', 'call_sign_no', 'engine_serial_number'], 'string', 'max' => 100],
            [['witness_name', 'witness_address', 'communication_equipment', 'fishing_equipment', 'navigation_equipment', 'approval_stage'], 'string', 'max' => 200],
            [['witness_nic'], 'string', 'max' => 50],
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
            'landing_site' => Yii::t('app', 'Landing Site'),
            'witness_name' => Yii::t('app', 'Witness Name'),
            'witness_address' => Yii::t('app', 'Witness Address'),
            'witness_nic' => Yii::t('app', 'Witness Nic'),
            'witness_singing_date' => Yii::t('app', 'Witness Singing Date'),
            'engine_make' => Yii::t('app', 'Engine Make'),
            'engine_horsepower' => Yii::t('app', 'Engine Horsepower'),
            'engine_serial_number' => Yii::t('app', 'Engine Serial Number'),
            'communication_equipment' => Yii::t('app', 'Communication Equipment'),
            'fishing_equipment' => Yii::t('app', 'Fishing Equipment'),
            'navigation_equipment' => Yii::t('app', 'Navigation Equipment'),
            'mea_report' => Yii::t('app', 'Mea Report'),
            'status' => Yii::t('app', 'Status'),
            'approval_stage' => Yii::t('app', 'Approval Stage'),
        ];
    }

    /**
     * Gets query for [[BoatNumber]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBoatNumber()
    {
        return $this->hasOne(BoatNumbers::class, ['id' => 'boat_number_id']);
    }

    /**
     * Gets query for [[Fisherman]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFisherman()
    {
        return $this->hasOne(ProfileFisherman::class, ['id' => 'fisherman_id']);
    }

    /**
     * Gets query for [[LandingSite]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLandingSite()
    {
        return $this->hasOne(MLandingSite::class, ['id' => 'landing_site']);
    }

    /**
     * Gets query for [[NationalLicenses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNationalLicenses()
    {
        return $this->hasMany(NationalLicense::class, ['boat_registration_id' => 'id']);
    }
}
