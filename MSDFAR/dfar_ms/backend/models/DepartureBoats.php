<?php

namespace backend\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use backend\models\BluetrakerReports;
/**
 * This is the model class for table "fisherman_registerd_boat".
 *
 * @property int $id
 * @property int|null $boat_number_id
 * @property int|null $fisherman_id
 * @property string $status
 * @property int $compulsory_service
 * @property string|null $timestamp
 * @property string $harbor
 * @property string $district
 * @property string $date_violation
 * @property string $dep_cancelled_by
 * @property string|null $dep_cancel_date
 * @property string $remarks
 * @property string $offence
 * @property string $to_date
 * @property string|null $compulsory_service_from_date
 * @property string|null $compulsory_service_to_date
 * @property int|null $VMS
 */
class DepartureBoats extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fisherman_registerd_boat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['boat_number_id', 'fisherman_id','compulsory_service'], 'integer'],
            [['status', 'harbor', 'district', 'dep_cancelled_by', 'remarks', 'offence'], 'required'],
            [['timestamp', 'dep_cancel_date', 'to_date', 'date_violation'], 'safe'],
            [['status', 'harbor', 'district', 'dep_cancelled_by', 'to_date'], 'string', 'max' => 100],
            [['date_violation', 'remarks', 'offence'], 'string', 'max' => 200],
            [['VMS'], 'default', 'value' => null],
            [['VMS'], 'in', 'range' => [0, 1]],
            [['compulsory_service_from_date', 'compulsory_service_to_date'], 'date', 'format' => 'php:Y-m-d'],
            [['compulsory_service_from_date', 'compulsory_service_to_date'], 'required',
            'when' => function ($model) {
                return $model->status === 'Temporary Service Allow';
            },
            'whenClient' => "function () {
                return $('#departureboats-status').val() === 'Temporary Service Allow';
            }",
            'message' => 'This field is required for Temporary Service Allow.'
        ],

        ['compulsory_service_to_date', 'compare',
            'compareAttribute' => 'compulsory_service_from_date',
            'operator' => '>=',
            'message' => 'To Date must be later than or equal to From Date.'
        ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'boat_number_id' => Yii::t('app', 'Boat Number'),
            'fisherman_id' => Yii::t('app', 'Owner Name'),
            'status' => Yii::t('app', 'Status'),
            'timestamp' => Yii::t('app', 'Timestamp'),
            'harbor' => Yii::t('app', 'Harbor'),
            'district' => Yii::t('app', 'Relevant District'),
            'date_violation' => Yii::t('app', 'Date of Violation (If applicable)'),
            'dep_cancelled_by' => Yii::t('app', 'Departure Cancelled By'),
            'dep_cancel_date' => Yii::t('app', 'Applicable From'),
            'remarks' => Yii::t('app', 'Reason'),
            'offence' => Yii::t('app', 'Details'),
            'to_date' => Yii::t('app', 'To'),
            'compulsory_service_from_date' => 'From Date',
            'compulsory_service_to_date' => 'To Date',
            'VMS' => 'VMS Status',
        ];
    }


    /**
     * Gets query for [[Owner0]].
     *
     * @return ActiveQuery
     */
    public function getBoat()
    {
        return $this->hasOne(BoatNumbers::class, ['id' => 'boat_number_id']);
    }

    public function getFisherman()
    {
        return $this->hasOne(ProfileFisherman::class, ['id' => 'fisherman_id']);
    }

    public function getLatestHighseasExpire()
    {
        return $this->getHighseasLicenses()
            ->select('expire_date')
            ->orderBy(['expire_date' => SORT_DESC])
            ->scalar();   // or ->limit(1)->one()->expire_date ?? null
    }

    public function getHighseasLicenses()
    {
        return $this->hasMany(HighseasLicense::class, ['boat_registration_id' => 'id']);
    }

    public function getLatestNationalExpire()
    {
        return $this->getNationalLicenses()
            ->max('expire_date');
//            ->select('expire_date')
//            ->orderBy(['expire_date' => SORT_DESC])
//            ->scalar();   // or ->limit(1)->one()->expire_date ?? null
    }

    public function getNationalLicenses()
    {
        return $this->hasMany(NationalLicense::class, ['boat_registration_id' => 'id']);
    }

    public function getLatestBoatRegExpire()
    {
        return $this->getBoatRegLicenses()
//            ->max('expire_date');
            ->select('expire_date')
            ->orderBy(['expire_date' => SORT_DESC])
            ->scalar();   // or ->limit(1)->one()->expire_date ?? null
    }

    public function getBoatRegLicenses()
    {
        return $this->hasMany(FishermanRegisterdBoatLicense::class, ['id' => 'id']);
    }

    public function getBluetrakerReports()
    {
        return $this->hasMany(
            BluetrakerReports::class,
            ['VesselName' => 'boat_number']
        )->via('boat');
    }

    public function getLatestBluetrakerReport()
{
    return $this->hasOne(
        BluetrakerReports::class,
        ['VesselName' => 'boat_number']
    )
    ->via('boat')
    ->alias('bt')
    ->andWhere(['<>', 'bt.Event', 0])
    ->orderBy([
        'bt.id' => SORT_DESC,
    ]);
}
}
