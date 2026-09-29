<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "fuel_data".
 *
 * @property int $id
 * @property int $boat_registration_id
 * @property int $bank_code
 * @property int $bank_branch
 * @property int $account_number
 * @property int $fuel_quota_cat
 * @property int $status
 * @property string $approval_stage
 * @property string $created
 * @property string|null $approved_time
 * @property FishermanRegisterdBoatLicense $boatRegistration
 * @property Banks $banks
 * @property BankBranches $bankbranches
 * @property FuelQuotaCategories $fuelquotacategories

 */
class FuelData extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fuel_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['approved_time'], 'default', 'value' => null],
            [['boat_registration_id', 'bank_code', 'bank_branch', 'account_number', 'fuel_quota_cat', 'status', 'approval_stage', 'created'], 'required'],
            [['boat_registration_id', 'bank_code', 'bank_branch', 'account_number', 'fuel_quota_cat', 'status'], 'integer'],
            [['created', 'approved_time'], 'safe'],
            [['approval_stage'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'boat_registration_id' => Yii::t('app', 'Boat Registration ID'),
            'bank_code' => Yii::t('app', 'Bank Name'),
            'bank_branch' => Yii::t('app', 'Bank Branch'),
            'account_number' => Yii::t('app', 'Account Number'),
            'fuel_quota_cat' => Yii::t('app', 'Fuel Quota Cat'),
            'status' => Yii::t('app', 'Status'),
            'approval_stage' => Yii::t('app', 'Approval Stage'),
            'created' => Yii::t('app', 'Created'),
            'approved_time' => Yii::t('app', 'Approved Time'),
        ];
    }

     public function getBoatRegistration()
    {
        return $this->hasOne(FishermanRegisterdBoatLicense::class, ['id' => 'boat_registration_id']);
    }

    public function getBankName()
    {
        return $this->hasOne(Banks::class, ['code' => 'bank_code']);
    }

    public function getBranchName()
{
           return $this->hasOne(BankBranches::class, ['id' => 'bank_branch']);

}

 public function getFuelCatdata()
{
           return $this->hasOne(FuelQuotaCategories::class, ['id' => 'fuel_quota_cat']);

}


}
