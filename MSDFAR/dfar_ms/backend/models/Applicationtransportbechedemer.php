<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "applicationtransportbechedemer".
 *
 * @property string $full_name
 * @property string $permanent_address
 * @property string $mailing_address
 * @property string $email
 * @property int $telephone
 * @property string $nic_number
 * @property int $id
 * @property int $company
 * @property int $business_reg_number
 * @property string|null $purchasing_district
 * @property string|null $store_district
 * @property string|null $final_destination
 * @property float|null $quantity
 * @property string|null $vehicle_number
 * @property string|null $boat_number
 * @property string|null $store_places
 * @property string|null $final_store_place
 * @property string|null $transport_number
 * @property string|null $vehicle_numbers
 * @property string|null $boat_numbers
 * @property string|null $store_place_details
 * @property resource|null $document
 * @property resource|null $supporting_document
 * @property string|null $vehicle
 * @property string|null $boat
 * @property string|null $species_type
 * @property string|null $weight_per_district
 * @property string|null $total_weight
 * @property string|null $intermediate_destination
 * @property string|null $quantity_pieces
 * @property int $status
 * @property string $approval_stage
 * @property string $created
 * @property string|null $approved_time
 * @property string|null $expire_date
 */
class Applicationtransportbechedemer extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'applicationtransportbechedemer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name', 'permanent_address', 'mailing_address', 'email', 'telephone', 'nic_number', 'company', 'business_reg_number', 'status', 'approval_stage'], 'required'],
            [['telephone', 'company', 'status'], 'integer'],
            [['quantity'], 'number'],
            [['store_places', 'document', 'supporting_document'], 'string'],
            [['created', 'approved_time', 'expire_date'], 'safe'],
            [['full_name', 'permanent_address', 'mailing_address', 'email'], 'string', 'max' => 100],
            [['nic_number', 'purchasing_district', 'store_district', 'final_destination', 'vehicle_number', 'boat_number'], 'string', 'max' => 20],
            [['final_store_place'], 'string', 'max' => 50],
            [['transport_number', 'vehicle_numbers', 'boat_numbers', 'store_place_details', 'vehicle', 'boat', 'species_type', 'weight_per_district', 'total_weight', 'intermediate_destination', 'quantity_pieces'], 'string', 'max' => 255],
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
            'mailing_address' => Yii::t('app', 'Mailing Address'),
            'email' => Yii::t('app', 'Email'),
            'telephone' => Yii::t('app', 'Telephone'),
            'nic_number' => Yii::t('app', 'Nic Number'),
            'id' => Yii::t('app', 'ID'),
            'company' => Yii::t('app', 'Company'),
            'business_reg_number' => Yii::t('app', 'Business Reg Number'),
            'purchasing_district' => Yii::t('app', 'Purchasing District'),
            'store_district' => Yii::t('app', 'Store District'),
            'final_destination' => Yii::t('app', 'Final Destination'),
            'quantity' => Yii::t('app', 'Quantity'),
            'vehicle_number' => Yii::t('app', 'Vehicle Number'),
            'boat_number' => Yii::t('app', 'Boat Number'),
            'store_places' => Yii::t('app', 'Store Places'),
            'final_store_place' => Yii::t('app', 'Final Store Place'),
            'transport_number' => Yii::t('app', 'Transport Number'),
            'vehicle_numbers' => Yii::t('app', 'Vehicle Numbers'),
            'boat_numbers' => Yii::t('app', 'Boat Numbers'),
            'store_place_details' => Yii::t('app', 'Store Place Details'),
            'document' => Yii::t('app', 'Document'),
            'supporting_document' => Yii::t('app', 'Supporting Document'),
            'vehicle' => Yii::t('app', 'Vehicle'),
            'boat' => Yii::t('app', 'Boat'),
            'species_type' => Yii::t('app', 'Species Type'),
            'weight_per_district' => Yii::t('app', 'Weight Per District'),
            'total_weight' => Yii::t('app', 'Total Weight'),
            'intermediate_destination' => Yii::t('app', 'Intermediate Destination'),
            'quantity_pieces' => Yii::t('app', 'Quantity Pieces'),
            'status' => Yii::t('app', 'Status'),
            'approval_stage' => Yii::t('app', 'Approval Stage'),
            'created' => Yii::t('app', 'Created'),
            'approved_time' => Yii::t('app', 'Approved Time'),
            'expire_date' => Yii::t('app', 'Expire Date'),
        ];
    }
}
