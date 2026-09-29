<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "m_fi_district".
 *
 * @property int $id
 * @property string $name
 * @property int $status
 * @property string|null $code
 *
 * @property MDivision[] $mDivisions
 */
class MFiDistrict extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'm_fi_district';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'status'], 'required'],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 200],
            [['code'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
            'code' => Yii::t('app', 'Code'),
        ];
    }

    // /**
    //  * {@inheritdoc}
    //  */
    // public static function primaryKey()
    // {
    //     return ['id'];
    // }

    /**
     * {@inheritdoc}
     */
    public static function primaryKey()
    {
        return ['id'];
    }

    /**
     * Gets query for [[MDivisions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMDivisions()
    {
        return $this->hasMany(MDivision::class, ['district_id' => 'id']);
    }
}
