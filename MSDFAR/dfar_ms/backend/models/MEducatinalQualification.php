<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "m_educatinal_qualification".
 *
 * @property int $id
 * @property string $description
 * @property int $status
 */
class MEducatinalQualification extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'm_educatinal_qualification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'status'], 'required'],
            [['status'], 'integer'],
            [['description'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'description' => Yii::t('app', 'Description'),
            'status' => Yii::t('app', 'Status'),
        ];
    }
}
