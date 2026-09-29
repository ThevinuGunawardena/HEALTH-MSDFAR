<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "deparure_request_crew".
 *
 * @property int $id
 * @property int $request_id
 * @property int $skipper_id
 * @property string $nic
 * @property string $name
 * @property string $mobile_number
 * @property DepartureRequests $request
 */
class DeparureRequestCrew extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'deparure_request_crew';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request_id', 'nic', 'name'], 'required'],
            [['request_id'], 'integer'],
            [['nic'], 'string', 'max' => 100],
            [['name'], 'string', 'max' => 200],
            ['mobile_number', 'trim'],
            ['mobile_number', 'string', 'max' => 20],
            [
            'mobile_number',
            'match',
            'pattern' => '/^[0-9+\-\s()]+$/',
            'message' => 'Please enter a valid mobile number.',
        ],
            [['request_id'], 'exist', 'skipOnError' => true, 'targetClass' => DepartureRequests::class, 'targetAttribute' => ['request_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'request_id' => Yii::t('app', 'Request ID'),
            'nic' => Yii::t('app', 'Nic'),
            'name' => Yii::t('app', 'Name'),
            'mobile_number' => 'Mobile Number',

        ];
    }

    /**
     * Gets query for [[Request]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequest()
    {
        return $this->hasOne(DepartureRequests::class, ['id' => 'request_id']);
    }
}
