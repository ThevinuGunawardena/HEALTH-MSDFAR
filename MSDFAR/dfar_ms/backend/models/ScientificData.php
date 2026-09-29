<?php

namespace backend\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "scientific_data".
 *
 * @property int $id
 * @property int $district
 * @property int $division
 * @property int $landing_site
 * @property string $start_time
 * @property string $end_time
 * @property int $added_by
 * @property int $status
 *
 * @property User $addedBy
 * @property MFiDistrict $district0
 * @property MDivision $division0
 * @property MLandingSite $landingSite
 * @property ScientificFleetData[] $scientificFleetDatas
 * @property ScientificSamplingData[] $scientificSamplingDatas
 */
class ScientificData extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'scientific_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['district', 'division', 'landing_site', 'start_time', 'end_time', 'added_by', 'status'], 'required'],
            [['district', 'division', 'landing_site', 'added_by', 'status'], 'integer'],
            [['start_time', 'end_time',"approval_stage",'inspected_by'], 'safe'],
            [['district'], 'exist', 'skipOnError' => true, 'targetClass' => MFiDistrict::class, 'targetAttribute' => ['district' => 'id']],
            [['division'], 'exist', 'skipOnError' => true, 'targetClass' => MDivision::class, 'targetAttribute' => ['division' => 'id']],
            [['landing_site'], 'exist', 'skipOnError' => true, 'targetClass' => MLandingSite::class, 'targetAttribute' => ['landing_site' => 'id']],
            [['added_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['added_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'district' => Yii::t('app', 'District'),
            'division' => Yii::t('app', 'Division'),
            'landing_site' => Yii::t('app', 'Landing Site'),
            'start_time' => Yii::t('app', 'Sampling date'),
            'end_time' => Yii::t('app', 'Submitted Time'),
            'added_by' => Yii::t('app', 'Inspection Done by'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Gets query for [[AddedBy]].
     *
     * @return ActiveQuery
     */
    public function getAddedBy()
    {
        return $this->hasOne(User::class, ['id' => 'added_by']);
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
     * Gets query for [[Division0]].
     *
     * @return ActiveQuery
     */
    public function getDivision0()
    {
        return $this->hasOne(MDivision::class, ['id' => 'division']);
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
     * Gets query for [[ScientificFleetDatas]].
     *
     * @return ActiveQuery
     */
    public function getScientificFleetDatas()
    {
        return $this->hasMany(ScientificFleetData::class, ['scientific_id' => 'id']);
    }

    /**
     * Gets query for [[ScientificSamplingDatas]].
     *
     * @return ActiveQuery
     */
    public function getScientificSamplingDatas()
    {
        return $this->hasMany(ScientificSamplingData::class, ['scientific_id' => 'id']);
    }
}
