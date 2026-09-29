<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "renew_boat_registration".
 *
 * @property int $id
 * @property int $reg_id
 * @property string $notes
 * @property int $status
 * @property string $approval_statge
 */
class RenewBoatRegistration extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'renew_boat_registration';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reg_id', 'notes', 'approval_statge'], 'required'],
            [['reg_id', 'status'], 'integer'],
            [['notes'], 'string', 'max' => 500],
            [['approval_statge'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'reg_id' => Yii::t('app', 'Reg ID'),
            'notes' => Yii::t('app', 'Notes'),
            'status' => Yii::t('app', 'Status'),
            'approval_statge' => Yii::t('app', 'Approval Statge'),
        ];
    }
}
