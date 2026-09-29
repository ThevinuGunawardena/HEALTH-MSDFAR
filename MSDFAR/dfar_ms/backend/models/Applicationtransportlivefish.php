<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "applicationtransportlivefish".
 *
 * @property string $full_name
 * @property string $permanent_address
 * @property int $telephone
 * @property int|null $fax_number
 * @property string $nic_number
 * @property int $id
 * @property int $company
 * @property int $business_reg_number
 * @property string|null $purchase_places
 * @property string|null $transport_route
 * @property string|null $vehicle_number
 * @property string|null $boat_number
 * @property string|null $contact_value
 * @property string|null $vehicle_boat_value
 * @property string|null $store_place_details
 * @property string|null $vehicle_numbers
 * @property string|null $boat_numbers
 * @property resource|null $supporting_document
 * @property string|null $species_type
 * @property string|null $weight_per_district
 * @property string|null $total_weight
 * @property string|null $intermediate_destination
 * @property string|null $final_destination
 * @property string|null $purchasing_district
 * @property string|null $vehicle
 * @property string|null $boat
 * @property string|null $request_date
 * @property int $status
 * @property string $approval_stage
 * @property string $created
 * @property string|null $approved_time
 * @property string|null $expire_date
 */
class Applicationtransportlivefish extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'applicationtransportlivefish';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name', 'permanent_address', 'telephone', 'nic_number', 'company', 'business_reg_number', 'status', 'approval_stage'], 'required'],
            [['telephone', 'fax_number', 'company', 'business_reg_number', 'status'], 'integer'],
            [['supporting_document'], 'string'],
            [['request_date', 'created', 'approved_time', 'expire_date'], 'safe'],
            [['full_name', 'permanent_address', 'purchase_places', 'transport_route'], 'string', 'max' => 100],
            [['nic_number', 'vehicle_number', 'boat_number', 'approval_stage'], 'string', 'max' => 50],
            [['contact_value', 'vehicle_boat_value', 'store_place_details', 'vehicle_numbers', 'boat_numbers', 'species_type', 'weight_per_district', 'total_weight', 'intermediate_destination', 'final_destination', 'purchasing_district', 'vehicle', 'boat'], 'string', 'max' => 255],
            [['tnc'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'full_name' => Yii::t('app', 'Full Name'),
            'permanent_address' => Yii::t('app', 'Permanent Address'),
            'telephone' => Yii::t('app', 'Telephone'),
            'fax_number' => Yii::t('app', 'Fax Number'),
            'nic_number' => Yii::t('app', 'Nic Number'),
            'id' => Yii::t('app', 'ID'),
            'company' => Yii::t('app', 'Company'),
            'business_reg_number' => Yii::t('app', 'Business Reg Number'),
            'purchase_places' => Yii::t('app', 'Purchase Places'),
            'transport_route' => Yii::t('app', 'Transport Route'),
            'vehicle_number' => Yii::t('app', 'Vehicle Number'),
            'boat_number' => Yii::t('app', 'Boat Number'),
            'contact_value' => Yii::t('app', 'Contact Value'),
            'vehicle_boat_value' => Yii::t('app', 'Vehicle Boat Value'),
            'store_place_details' => Yii::t('app', 'Store Place Details'),
            'vehicle_numbers' => Yii::t('app', 'Vehicle Numbers'),
            'boat_numbers' => Yii::t('app', 'Boat Numbers'),
            'supporting_document' => Yii::t('app', 'Supporting Document'),
            'species_type' => Yii::t('app', 'Species Type'),
            'weight_per_district' => Yii::t('app', 'Weight Per District'),
            'total_weight' => Yii::t('app', 'Total Weight'),
            'intermediate_destination' => Yii::t('app', 'Intermediate Destination'),
            'final_destination' => Yii::t('app', 'Final Destination'),
            'purchasing_district' => Yii::t('app', 'Purchasing District'),
            'vehicle' => Yii::t('app', 'Vehicle'),
            'boat' => Yii::t('app', 'Boat'),
            'request_date' => Yii::t('app', 'Request Date'),
            'status' => Yii::t('app', 'Status'),
            'approval_stage' => Yii::t('app', 'Approval Stage'),
            'created' => Yii::t('app', 'Created'),
            'approved_time' => Yii::t('app', 'Approved Time'),
            'expire_date' => Yii::t('app', 'Expire Date'),
        ];
    }
}
