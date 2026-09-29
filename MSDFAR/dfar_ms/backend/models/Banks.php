<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "banks".
 *
 * @property int $id
 * @property string $bank_name
 * @property int $code
 */
class Banks extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'banks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bank_name', 'code'], 'required'],
            [['code'], 'integer'],
            [['bank_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'bank_name' => Yii::t('app', 'Bank Name'),
            'code' => Yii::t('app', 'Code'),
        ];
    }

}
