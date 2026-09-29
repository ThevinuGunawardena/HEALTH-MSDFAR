<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "id_print_queue".
 *
 * @property int $id
 * @property int $type
 * @property int $user_id
 * @property int $print_id
 * @property int $status
 */
class IdPrintQueue extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'id_print_queue';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'user_id', 'print_id', 'status'], 'required'],
            [['type', 'user_id', 'print_id', 'status'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'user_id' => Yii::t('app', 'User ID'),
            'print_id' => Yii::t('app', 'Print ID'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

}
