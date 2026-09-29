<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "m_traning_institute_programs".
 *
 * @property int $id
 * @property int $institute_id
 * @property string $name
 * @property int $status
 */
class MTraningInstitutePrograms extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'm_traning_institute_programs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['institute_id', 'name'], 'required'],
            [['institute_id', 'status'], 'integer'],
            [['name'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'institute_id' => Yii::t('app', 'Institute ID'),
            'name' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
        ];
    }
}
