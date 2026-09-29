<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "bluetraker_reports".
 *
 * @property int $Id
 * @property int $MessageId
 * @property string $VesselName
 * @property int $PublicDeviceId
 * @property string $CreatedGpsTime
 * @property string $ReceiveTime
 * @property float $Longitude
 * @property float $Latitude
 * @property int|null $Heading
 * @property float|null $Speed
 * @property int $Event
 * @property string $CreatedAt
 */
class BluetrakerReports extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bluetraker_reports';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Heading', 'Speed'], 'default', 'value' => null],
            [['MessageId', 'VesselName', 'PublicDeviceId', 'CreatedGpsTime', 'ReceiveTime', 'Longitude', 'Latitude', 'Event'], 'required'],
            [['MessageId', 'PublicDeviceId', 'Heading', 'Event'], 'integer'],
            [['CreatedGpsTime', 'ReceiveTime', 'CreatedAt'], 'safe'],
            [['Longitude', 'Latitude', 'Speed'], 'number'],
            [['VesselName'], 'string', 'max' => 100],
            [['MessageId'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => Yii::t('app', 'ID'),
            'MessageId' => Yii::t('app', 'Message ID'),
            'VesselName' => Yii::t('app', 'Vessel Name'),
            'PublicDeviceId' => Yii::t('app', 'Public Device ID'),
            'CreatedGpsTime' => Yii::t('app', 'Created Gps Time'),
            'ReceiveTime' => Yii::t('app', 'Receive Time'),
            'Longitude' => Yii::t('app', 'Longitude'),
            'Latitude' => Yii::t('app', 'Latitude'),
            'Heading' => Yii::t('app', 'Heading'),
            'Speed' => Yii::t('app', 'Speed'),
            'Event' => Yii::t('app', 'Event'),
            'CreatedAt' => Yii::t('app', 'Created At'),
        ];
    }

}
