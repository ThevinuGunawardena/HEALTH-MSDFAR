<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "special_altereration".
 *
 * @property int $id
 * @property int $userId
 * @property string $title
 * @property string $desciption
 * @property string|null $file
 */
class SpecialAltereration extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'special_altereration';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userId', 'title', 'desciption'], 'required'],
            [['userId'], 'integer'],
            [['title', 'file'], 'string', 'max' => 200],
            [['desciption'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'userId' => Yii::t('app', 'User ID'),
            'title' => Yii::t('app', 'Title'),
            'desciption' => Yii::t('app', 'Desciption'),
            'file' => Yii::t('app', 'File'),
        ];
    }
}
