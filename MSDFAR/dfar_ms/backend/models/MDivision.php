<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "m_division".
 *
 * @property int $id
 * @property int $district_id
 * @property string $name
 * @property int $status
 *
 * @property MFiDistrict $district
 */
class MDivision extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'm_division';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['district_id', 'name', 'status'], 'required'],
            [['district_id', 'status'], 'integer'],
            [['name'], 'string', 'max' => 200],
            [['district_id'], 'exist', 'skipOnError' => true, 'targetClass' => MFiDistrict::class, 'targetAttribute' => ['district_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'district_id' => Yii::t('app', 'District ID'),
            'name' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Gets query for [[District]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDistrict()
    {
        return $this->hasOne(MFiDistrict::class, ['id' => 'district_id']);
    }
}
