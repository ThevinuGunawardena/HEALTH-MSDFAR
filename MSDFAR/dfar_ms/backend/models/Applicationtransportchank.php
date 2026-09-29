<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "applicationtransportchank".
 *
 * @property string $full_name
 * @property string $permanent_address
 * @property string $telephone_no
 * @property string $email
 * @property string $national_id
 * @property int $id
 * @property int $company
 * @property string $business_registration_no
 * @property string|null $purchasing_district
 * @property string|null $processing_district
 * @property string|null $final_storing_district
 * @property string|null $store_place
 * @property string|null $final_store_place
 * @property resource|null $supporting_document
 * @property int|null $quantity_kg
 * @property int|null $quantity_pieces
 * @property string|null $quantity_type
 * @property string|null $contact_value
 * @property string|null $quantity_value
 * @property string|null $store_place_details
 * @property string|null $species_type
 * @property string|null $weight_per_district
 * @property string|null $total_weight
 * @property string|null $intermediate_destination
 * @property string|null $final_destination
 * @property string|null $vehicle_numbers
 * @property string|null $boat_numbers
 * @property string|null $vehicle
 * @property string|null $boat
 * @property string|null $nic_number
 * @property string|null $business_reg_number
 * @property string|null $request_date
 * @property int $status
 * @property string $approval_stage
 * @property string $created
 * @property string|null $approved_time
 * @property string|null $expire_date
 */
class Applicationtransportchank extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'applicationtransportchank';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name', 'permanent_address', 'telephone_no', 'email', 'national_id', 'company', 'business_registration_no', 'status', 'approval_stage'], 'required'],
            [['company', 'quantity_kg', 'quantity_pieces', 'status'], 'integer'],
            [['supporting_document'], 'string'],
            [['request_date', 'created', 'approved_time', 'expire_date'], 'safe'],
            [['full_name', 'permanent_address', 'email', 'store_place', 'weight_per_district', 'total_weight'], 'string', 'max' => 100],
            [['telephone_no'], 'string', 'max' => 20],
            [['national_id', 'business_registration_no', 'purchasing_district', 'processing_district', 'final_storing_district', 'final_store_place', 'approval_stage'], 'string', 'max' => 50],
            [['quantity_type', 'contact_value', 'quantity_value', 'store_place_details', 'species_type', 'intermediate_destination', 'final_destination', 'vehicle_numbers', 'boat_numbers', 'vehicle', 'boat', 'nic_number', 'business_reg_number'], 'string', 'max' => 255],
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
            'telephone_no' => Yii::t('app', 'Telephone No'),
            'email' => Yii::t('app', 'Email'),
            'national_id' => Yii::t('app', 'National ID'),
            'id' => Yii::t('app', 'ID'),
            'company' => Yii::t('app', 'Company'),
            'business_registration_no' => Yii::t('app', 'Business Registration No'),
            'purchasing_district' => Yii::t('app', 'Purchasing District'),
            'processing_district' => Yii::t('app', 'Processing District'),
            'final_storing_district' => Yii::t('app', 'Final Storing District'),
            'store_place' => Yii::t('app', 'Store Place'),
            'final_store_place' => Yii::t('app', 'Final Store Place'),
            'supporting_document' => Yii::t('app', 'Supporting Document'),
            'quantity_kg' => Yii::t('app', 'Quantity Kg'),
            'quantity_pieces' => Yii::t('app', 'Quantity Pieces'),
            'quantity_type' => Yii::t('app', 'Quantity Type'),
            'contact_value' => Yii::t('app', 'Contact Value'),
            'quantity_value' => Yii::t('app', 'Quantity Value'),
            'store_place_details' => Yii::t('app', 'Store Place Details'),
            'species_type' => Yii::t('app', 'Species Type'),
            'weight_per_district' => Yii::t('app', 'Weight Per District'),
            'total_weight' => Yii::t('app', 'Total Weight'),
            'intermediate_destination' => Yii::t('app', 'Intermediate Destination'),
            'final_destination' => Yii::t('app', 'Final Destination'),
            'vehicle_numbers' => Yii::t('app', 'Vehicle Numbers'),
            'boat_numbers' => Yii::t('app', 'Boat Numbers'),
            'vehicle' => Yii::t('app', 'Vehicle'),
            'boat' => Yii::t('app', 'Boat'),
            'nic_number' => Yii::t('app', 'Nic Number'),
            'business_reg_number' => Yii::t('app', 'Business Reg Number'),
            'request_date' => Yii::t('app', 'Request Date'),
            'status' => Yii::t('app', 'Status'),
            'approval_stage' => Yii::t('app', 'Approval Stage'),
            'created' => Yii::t('app', 'Created'),
            'approved_time' => Yii::t('app', 'Approved Time'),
            'expire_date' => Yii::t('app', 'Expire Date'),
        ];
    }
}
