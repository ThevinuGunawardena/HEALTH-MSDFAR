<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "applicationtransportnakla".
 *
 * @property string $full_name
 * @property string $permanent_address
 * @property int|null $fax_number
 * @property string $nic_number
 * @property int $id
 * @property int $company
 * @property int $business_reg_number
 * @property string|null $destination_place
 * @property float|null $quantity_kg
 * @property float|null $quantity_pieces
 * @property string|null $vehicle_number
 * @property string|null $boat_number
 * @property string|null $store_places
 * @property string|null $contact_value
 * @property string|null $quantity_value
 * @property string|null $transport_value
 * @property string|null $vehicle_numbers
 * @property string|null $boat_numbers
 * @property string|null $store_place_details
 * @property string|null $contact_type
 * @property resource|null $document
 * @property string|null $supporting_document
 * @property string|null $species_type
 * @property string|null $weight_per_district
 * @property string|null $total_weight
 * @property string|null $intermediate_destination
 * @property string|null $final_destination
 * @property string|null $vehicle
 * @property string|null $boat
 * @property string|null $purchasing_district
 * @property int $telephone
 * @property int|null $telephone_number
 * @property string|null $request_date
 * @property int $status
 * @property string $approval_stage
 * @property string $created
 * @property string|null $approved_time
 * @property string|null $expire_date
 */
class Applicationtransportnakla extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'applicationtransportnakla';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name', 'permanent_address', 'nic_number', 'company', 'business_reg_number', 'telephone', 'status', 'approval_stage'], 'required'],
            [['fax_number', 'company', 'business_reg_number', 'telephone', 'telephone_number', 'status'], 'integer'],
            [['quantity_kg', 'quantity_pieces'], 'number'],
            [['document'], 'string'],
            [['request_date', 'created', 'approved_time', 'expire_date'], 'safe'],
            [['full_name', 'permanent_address', 'store_places'], 'string', 'max' => 100],
            [['nic_number', 'destination_place', 'vehicle_number', 'boat_number', 'approval_stage'], 'string', 'max' => 50],
            [['tnc'], 'string'],
            [['contact_value', 'quantity_value', 'transport_value', 'vehicle_numbers', 'boat_numbers', 'store_place_details', 'contact_type', 'supporting_document', 'species_type', 'weight_per_district', 'total_weight', 'intermediate_destination', 'final_destination', 'vehicle', 'boat', 'purchasing_district'], 'string', 'max' => 255],
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
            'fax_number' => Yii::t('app', 'Fax Number'),
            'nic_number' => Yii::t('app', 'Nic Number'),
            'id' => Yii::t('app', 'ID'),
            'company' => Yii::t('app', 'Company'),
            'business_reg_number' => Yii::t('app', 'Business Reg Number'),
            'destination_place' => Yii::t('app', 'Destination Place'),
            'quantity_kg' => Yii::t('app', 'Quantity Kg'),
            'quantity_pieces' => Yii::t('app', 'Quantity Pieces'),
            'vehicle_number' => Yii::t('app', 'Vehicle Number'),
            'boat_number' => Yii::t('app', 'Boat Number'),
            'store_places' => Yii::t('app', 'Store Places'),
            'contact_value' => Yii::t('app', 'Contact Value'),
            'quantity_value' => Yii::t('app', 'Quantity Value'),
            'transport_value' => Yii::t('app', 'Transport Value'),
            'vehicle_numbers' => Yii::t('app', 'Vehicle Numbers'),
            'boat_numbers' => Yii::t('app', 'Boat Numbers'),
            'store_place_details' => Yii::t('app', 'Store Place Details'),
            'contact_type' => Yii::t('app', 'Contact Type'),
            'document' => Yii::t('app', 'Document'),
            'supporting_document' => Yii::t('app', 'Supporting Document'),
            'species_type' => Yii::t('app', 'Species Type'),
            'weight_per_district' => Yii::t('app', 'Weight Per District'),
            'total_weight' => Yii::t('app', 'Total Weight'),
            'intermediate_destination' => Yii::t('app', 'Intermediate Destination'),
            'final_destination' => Yii::t('app', 'Final Destination'),
            'vehicle' => Yii::t('app', 'Vehicle'),
            'boat' => Yii::t('app', 'Boat'),
            'purchasing_district' => Yii::t('app', 'Purchasing District'),
            'telephone' => Yii::t('app', 'Telephone'),
            'telephone_number' => Yii::t('app', 'Telephone Number'),
            'request_date' => Yii::t('app', 'Request Date'),
            'status' => Yii::t('app', 'Status'),
            'approval_stage' => Yii::t('app', 'Approval Stage'),
            'created' => Yii::t('app', 'Created'),
            'approved_time' => Yii::t('app', 'Approved Time'),
            'expire_date' => Yii::t('app', 'Expire Date'),
        ];
    }
}
