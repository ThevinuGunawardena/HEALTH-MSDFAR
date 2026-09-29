<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "m_gear_type_extra_data".
 *
 * @property int $id
 * @property int $group_id
 * @property string $name
 * @property string $slug
 * @property int $status
 *
 * @property MGearTypes[] $mGearTypes
 */
class MGearTypeExtraData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'm_gear_type_extra_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group_id', 'name', 'slug', 'status'], 'required'],
            [['group_id', 'status'], 'integer'],
            [['name', 'slug'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'group_id' => Yii::t('app', 'Group ID'),
            'name' => Yii::t('app', 'Name'),
            'slug' => Yii::t('app', 'Slug'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Gets query for [[MGearTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMGearTypes()
    {
        return $this->hasMany(MGearTypes::class, ['sub_group' => 'group_id']);
    }
}
