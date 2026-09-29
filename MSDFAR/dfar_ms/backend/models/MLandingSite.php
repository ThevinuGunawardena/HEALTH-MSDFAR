<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "m_landing_site".
 *
 * @property int $id
 * @property int $division_id
 * @property string $code
 * @property string $name
 * @property int $scientific_code
 * @property int $status
 * @property MDivision $division
 */
class MLandingSite extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'm_landing_site';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['division_id', 'code', 'name', 'status'], 'required'],
            [['division_id', 'status', 'scientific_code'], 'integer'],
            [['name', 'code'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'division_id' => Yii::t('app', 'Division'),
            'code' => Yii::t('app', 'Code'),
            'name' => Yii::t('app', 'Name'),
            'scientific_code' => Yii::t('app', 'Scientific Code'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    public function getDivision0()
    {
        return $this->hasOne(MDivision::class, ['id' => 'division_id']);
    }
}
