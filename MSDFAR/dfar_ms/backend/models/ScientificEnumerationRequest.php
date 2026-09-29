<?php

namespace backend\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "scientific_enumeration_request".
 *
 * @property int $id
 * @property int $user
 * @property string|null $request_date
 * @property string $date
 * @property string $can_continue
 * @property string|null $reson
 * @property int $status
 * @property int $district
 * @property int $division
 * @property int $landing_site
 *
 * @property MFiDistrict $district0
 * @property MDivision $division0
 * @property MLandingSite $landingSite
 */
class ScientificEnumerationRequest extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'scientific_enumeration_request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user', 'date', 'can_continue', 'status', 'district', 'division', 'landing_site', 'date', 'request_date'], 'required'],
            [['user', 'status', 'district', 'division', 'landing_site'], 'integer'],

            [['can_continue'], 'string', 'max' => 100],
            [['reson'], 'string', 'max' => 200],
            [['district'], 'exist', 'skipOnError' => true, 'targetClass' => MFiDistrict::class, 'targetAttribute' => ['district' => 'id']],
            [['division'], 'exist', 'skipOnError' => true, 'targetClass' => MDivision::class, 'targetAttribute' => ['division' => 'id']],
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
            'user' => Yii::t('app', 'User'),
            'request_date' => Yii::t('app', 'Sampling Date'),
            'date' => Yii::t('app', 'Submitted Date Time'),
            'can_continue' => Yii::t('app', 'Can Continue'),
            'reson' => Yii::t('app', 'Reason'),
            'status' => Yii::t('app', 'Status'),
            'district' => Yii::t('app', 'District'),
            'division' => Yii::t('app', 'Division'),
            'landing_site' => Yii::t('app', 'Landing Site'),
        ];
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
}
