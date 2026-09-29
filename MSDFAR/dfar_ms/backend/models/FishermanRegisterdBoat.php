<?php

namespace backend\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "fisherman_registerd_boat".
 *
 * @property int $id
 * @property int|null $boat_number_id
 * @property int|null $fisherman_id
 *
 * @property BoatNumbers $boatNumber
 * @property ProfileFisherman $fisherman
 *  * @property HighseasLicense[] $highseasLicenses
 *  * @property NationalLicense[] $nationalLicenses
 */
class FishermanRegisterdBoat extends ActiveRecord
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
            [['boat_number_id', 'fisherman_id'], 'integer'],
            [['boat_number_id'], 'exist', 'skipOnError' => true, 'targetClass' => BoatNumbers::class, 'targetAttribute' => ['boat_number_id' => 'id']],
            [['fisherman_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProfileFisherman::class, 'targetAttribute' => ['fisherman_id' => 'id']],
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
     * Gets query for [[HighseasLicenses]].
     *
     * @return ActiveQuery
     */
    public function getHighseasLicenses()
    {
        return $this->hasMany(HighseasLicense::class, ['boat_registration_id' => 'id']);
    }
    /**
     * Gets query for [[NationalLicenses]].
     *
     * @return ActiveQuery
     */
    public function getNationalLicenses()
    {
        return $this->hasMany(NationalLicense::class, ['boat_registration_id' => 'id']);
    }

}
