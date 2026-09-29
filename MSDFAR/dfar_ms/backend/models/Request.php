<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "request".
 *
 * @property int $id
 * @property string|null $boat_no
 * @property string $boat_name
 * @property string|null $owner
 * @property string|null $contact_no
 * @property string|null $email
 * @property string|null $skipper
 * @property string|null $skipper_no
 * @property string|null $skipper_nic
 * @property string|null $district
 * @property string|null $harbor
 * @property string|null $fishing_area
 * @property float|null $length_longline
 * @property float|null $length_gillnet
 * @property float|null $length_ringnet
 * @property int|null $longline_hooks
 * @property float|null $mesh_gillnet
 * @property float|null $mesh_ringnet
 * @property string|null $crew1
 * @property string|null $crew1_id
 * @property string|null $crew2
 * @property string|null $crew2_id
 * @property string|null $crew3
 * @property string|null $crew3_id
 * @property string|null $crew4
 * @property string|null $crew4_id
 * @property string|null $crew5
 * @property string|null $crew5_id
 * @property string|null $crew6
 * @property string|null $crew6_id
 * @property string|null $crew7
 * @property string|null $crew7_id
 * @property string|null $crew8
 * @property string|null $crew8_id
 * @property string|null $national_license_no
 * @property string|null $hs_license_no
 * @property string|null $vms
 * @property string|null $agree
 * @property string|null $req_date_time
 * @property string|null $user
 * @property string|null $action_date
 * @property string|null $approve
 * @property string|null $remarks
 * @property string|null $water_bot
 * @property string|null $mcs
 * @property string|null $frequency
 * @property string|null $vms_code
 * @property string $manual
 * @property string|null $arrivalPort
 * @property string|null $arrivalDate
 * @property string|null $arrTime
 */
class Request extends ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['boat_no', 'owner', 'contact_no', 'email', 'skipper', 'skipper_no', 'skipper_nic', 'district', 'harbor', 'fishing_area', 'length_longline', 'length_gillnet', 'length_ringnet', 'longline_hooks', 'mesh_gillnet', 'mesh_ringnet', 'crew1', 'crew1_id', 'crew2', 'crew2_id', 'crew3', 'crew3_id', 'crew4', 'crew4_id', 'crew5', 'crew5_id', 'crew6', 'crew6_id', 'crew7', 'crew7_id', 'crew8', 'crew8_id', 'national_license_no', 'hs_license_no', 'vms', 'agree', 'req_date_time', 'user', 'action_date', 'approve', 'remarks', 'water_bot', 'mcs', 'frequency', 'vms_code', 'arrivalPort', 'arrivalDate', 'arrTime'], 'default', 'value' => null],
            [['id', 'boat_name', 'manual'], 'required'],
            [['id', 'longline_hooks'], 'integer'],
            [['length_longline', 'length_gillnet', 'length_ringnet', 'mesh_gillnet', 'mesh_ringnet'], 'number'],
            [['req_date_time', 'action_date'], 'safe'],
            [['boat_no'], 'string', 'max' => 20],
            [['boat_name', 'vms_code', 'arrivalPort', 'arrivalDate', 'arrTime'], 'string', 'max' => 80],
            [['owner', 'email', 'skipper', 'district', 'user', 'remarks', 'mcs', 'frequency'], 'string', 'max' => 225],
            [['contact_no', 'skipper_nic', 'crew1_id', 'crew2_id', 'crew3_id', 'crew4_id', 'crew5_id', 'crew6_id', 'crew7_id', 'water_bot'], 'string', 'max' => 15],
            [['skipper_no'], 'string', 'max' => 50],
            [['harbor', 'crew1', 'crew2', 'crew3', 'crew4', 'crew5', 'crew6', 'crew7', 'crew8'], 'string', 'max' => 100],
            [['fishing_area', 'crew8_id', 'national_license_no', 'hs_license_no'], 'string', 'max' => 25],
            [['vms', 'agree', 'approve'], 'string', 'max' => 10],
            [['manual'], 'string', 'max' => 6],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'boat_no' => 'Boat No',
            'boat_name' => 'Boat Name',
            'owner' => 'Owner',
            'contact_no' => 'Contact No',
            'email' => 'Email',
            'skipper' => 'Skipper',
            'skipper_no' => 'Skipper No',
            'skipper_nic' => 'Skipper Nic',
            'district' => 'District',
            'harbor' => 'Harbor',
            'fishing_area' => 'Fishing Area',
            'length_longline' => 'Length Longline',
            'length_gillnet' => 'Length Gillnet',
            'length_ringnet' => 'Length Ringnet',
            'longline_hooks' => 'Longline Hooks',
            'mesh_gillnet' => 'Mesh Gillnet',
            'mesh_ringnet' => 'Mesh Ringnet',
            'crew1' => 'Crew1',
            'crew1_id' => 'Crew1 ID',
            'crew2' => 'Crew2',
            'crew2_id' => 'Crew2 ID',
            'crew3' => 'Crew3',
            'crew3_id' => 'Crew3 ID',
            'crew4' => 'Crew4',
            'crew4_id' => 'Crew4 ID',
            'crew5' => 'Crew5',
            'crew5_id' => 'Crew5 ID',
            'crew6' => 'Crew6',
            'crew6_id' => 'Crew6 ID',
            'crew7' => 'Crew7',
            'crew7_id' => 'Crew7 ID',
            'crew8' => 'Crew8',
            'crew8_id' => 'Crew8 ID',
            'national_license_no' => 'National License No',
            'hs_license_no' => 'Hs License No',
            'vms' => 'Vms',
            'agree' => 'Agree',
            'req_date_time' => 'Req Date Time',
            'user' => 'User',
            'action_date' => 'Action Date',
            'approve' => 'Approve',
            'remarks' => 'Remarks',
            'water_bot' => 'Water Bot',
            'mcs' => 'Mcs',
            'frequency' => 'Frequency',
            'vms_code' => 'Vms Code',
            'manual' => 'Manual',
            'arrivalPort' => 'Arrival Port',
            'arrivalDate' => 'Arrival Date',
            'arrTime' => 'Arr Time',
        ];
    }

}
