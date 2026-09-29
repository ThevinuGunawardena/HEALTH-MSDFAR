<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "applicationtransportlobster".
 *
 * @property string $full_name
 * @property string $permanent_address
 * @property string $applicant_address
 * @property int $telephone_number
 * @property int|null $fax_number
 * @property int $id
 * @property int $company
 * @property float|null $quantity_kg
 * @property float|null $quantity_pieces
 * @property string|null $transport_methods
 * @property string|null $contact_value
 * @property string|null $quantity_value
 * @property string|null $nic_number
 * @property string|null $store_place_details
 * @property string|null $vehicle
 * @property string|null $boat
 * @property resource|null $supporting_document
 * @property string|null $species_type
 * @property string|null $weight_per_district
 * @property string|null $total_weight
 * @property string|null $intermediate_destination
 * @property string|null $final_destination
 * @property string|null $purchasing_district
 * @property string|null $transport_route
 * @property string|null $request_date
 * @property int $status
 * @property string $approval_stage
 * @property string $created
 * @property string|null $approved_time
 * @property string|null $expire_date
 */
class Applicationtransportlobster extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'applicationtransportlobster';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name', 'permanent_address', 'applicant_address', 'telephone_number', 'company', 'status', 'approval_stage'], 'required'],
            [['telephone_number', 'fax_number', 'company', 'status'], 'integer'],
            [['quantity_kg', 'quantity_pieces'], 'number'],
            [['business_reg_number'], 'safe'],
            [['request_date', 'created', 'approved_time', 'expire_date'], 'safe'],
            [['full_name', 'permanent_address', 'applicant_address'], 'string', 'max' => 100],
            [['transport_methods', 'approval_stage'], 'string', 'max' => 50],
            [['contact_value', 'quantity_value', 'nic_number', 'store_place_details', 'vehicle', 'boat', 'species_type', 'weight_per_district', 'total_weight', 'intermediate_destination', 'final_destination', 'purchasing_district', 'transport_route'], 'string', 'max' => 255],
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
            'applicant_address' => Yii::t('app', 'Applicant Address'),
            'telephone_number' => Yii::t('app', 'Telephone Number'),
            'fax_number' => Yii::t('app', 'Fax Number'),
            'id' => Yii::t('app', 'ID'),
            'company' => Yii::t('app', 'Company'),
            'quantity_kg' => Yii::t('app', 'Quantity Kg'),
            'quantity_pieces' => Yii::t('app', 'Quantity Pieces'),
            'transport_methods' => Yii::t('app', 'Transport Methods'),
            'contact_value' => Yii::t('app', 'Contact Value'),
            'quantity_value' => Yii::t('app', 'Quantity Value'),
            'nic_number' => Yii::t('app', 'Nic Number'),
            'business_reg_number' => Yii::t('app', 'Business Reg Number'),
            'store_place_details' => Yii::t('app', 'Store Place Details'),
            'vehicle' => Yii::t('app', 'Vehicle'),
            'boat' => Yii::t('app', 'Boat'),
            'supporting_document' => Yii::t('app', 'Supporting Document'),
            'species_type' => Yii::t('app', 'Species Type'),
            'weight_per_district' => Yii::t('app', 'Weight Per District'),
            'total_weight' => Yii::t('app', 'Total Weight'),
            'intermediate_destination' => Yii::t('app', 'Intermediate Destination'),
            'final_destination' => Yii::t('app', 'Final Destination'),
            'purchasing_district' => Yii::t('app', 'Purchasing District'),
            'transport_route' => Yii::t('app', 'Transport Route'),
            'request_date' => Yii::t('app', 'Request Date'),
            'status' => Yii::t('app', 'Status'),
            'approval_stage' => Yii::t('app', 'Approval Stage'),
            'created' => Yii::t('app', 'Created'),
            'approved_time' => Yii::t('app', 'Approved Time'),
            'expire_date' => Yii::t('app', 'Expire Date'),
        ];
    }
}
