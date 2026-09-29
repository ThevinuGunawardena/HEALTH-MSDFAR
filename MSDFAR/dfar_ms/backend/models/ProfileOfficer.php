<?php

namespace backend\models;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "profile_officer".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $nic
 * @property string|null $current_designation
 * @property string|null $ministry_dept
 * @property string|null $current_workplace_type
 * @property string|null $current_workplace
 * @property string|null $district_office
 * @property string|null $public_service_appointment_date
 * @property string|null $appointment_letter
 * @property string|null $dfar_appointment_date
 * @property string|null $dfar_appointment_letter
 * @property string|null $recruitment_method
 * @property string|null $w_op_number
 * @property string|null $appointment_status
 * @property int|null $district
 * @property int|null $division
 * @property string|null $signature
 * @property string|null $profile_image
 * @property string|null $agreement
 * @property int $status
 * @property string $user_level
 * @property string|null $device_serial
 * @property string|null $it_result_sheet
 * @property string|null $cetificate
 * @property int $privacy_policy
 */
class ProfileOfficer extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'profile_officer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['personal_email', 'permanent_address'], 'required'],
            [['place_of_birth', 'date_of_birth'], 'safe'],
            [['district', 'division', 'status'], 'integer'],
            [['personal_email', 'place_of_birth', 'permanent_address'], 'string', 'max' => 200],
            [['profile_image', 'agreement', 'it_result_sheet', 'cetificate'], 'string', 'max' => 100],
            [['mobile_phone', 'home_phone'], 'string', 'max' => 10],

            // -- Identity & appointment details editable on the profile form --
            // Without these rules load() silently drops the fields and they
            // never save (first/last name, NIC, designation, appointment
            // dates, ministry, etc.).
            [['first_name', 'last_name'], 'required'],
            [['public_service_appointment_date', 'dfar_appointment_date'], 'safe'],
            [['first_name', 'last_name', 'appointment_letter', 'dfar_appointment_letter'], 'string', 'max' => 200],
            [['nic'], 'string', 'max' => 20],
            [['ministry_dept'], 'string', 'max' => 255],
            [['current_designation', 'current_workplace', 'district_office', 'recruitment_method', 'device_serial'], 'string', 'max' => 100],
            [['current_workplace_type', 'w_op_number', 'appointment_status', 'user_level'], 'string', 'max' => 50],

            [['district'], 'exist', 'skipOnError' => true, 'targetClass' => MFiDistrict::class, 'targetAttribute' => ['district' => 'id']],
            [['division'], 'exist', 'skipOnError' => true, 'targetClass' => MDivision::class, 'targetAttribute' => ['division' => 'id']],
            ['division', 'required', 'when' => function ($model) {
                return UserTypeUtil::hasType(Constant::FI);
            }, 'enableClientValidation' => false],
            ['district', 'required', 'when' => function ($model) {
                return !UserTypeUtil::hasType(Constant::DG) 
                    && !UserTypeUtil::hasType(Constant::DM) 
                    && !UserTypeUtil::hasType(Constant::DIRECTOR) 
                    && !UserTypeUtil::hasType(Constant::ICT_OFFICER)
                    && !UserTypeUtil::hasType(Constant::FISHERIES_OFFICER)  
                    && !UserTypeUtil::hasType(Constant::DEVELOPMENT_OFFICER)
                    && !UserTypeUtil::hasType(Constant::DEVELOPMENT_DIVISION) 
                    && !UserTypeUtil::hasType(Constant::MANAGEMENT_SERVICE_OFFICER) 
                    && !UserTypeUtil::hasType(Constant::CC) 
                    && !UserTypeUtil::hasType(Constant::KKS) 
                    && !UserTypeUtil::hasType(Constant::REPORT_OFFICER);
            }, 'enableClientValidation' => false],
            ['harbour', 'required', 'when' => function ($model) {
                return UserTypeUtil::hasType(Constant::HARBOUR_OFFICER);
            }, 'enableClientValidation' => false],
            [['privacy_policy'], 'compare', 'compareValue' => 1, 'message' => 'You must agree to the Privacy Policy.'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'nic' => Yii::t('app', 'NIC'),
            'current_designation' => Yii::t('app', 'Current Designation'),
            'ministry_dept' => Yii::t('app', 'Ministry / Department'),
            'current_workplace_type' => Yii::t('app', 'Current Workplace Type'),
            'public_service_appointment_date' => Yii::t('app', 'Public Service Appointment Date'),
            'appointment_letter' => Yii::t('app', 'Appointment Letter'),
            'dfar_appointment_date' => Yii::t('app', 'DFAR Appointment Date'),
            'dfar_appointment_letter' => Yii::t('app', 'DFAR Appointment Letter'),
            'recruitment_method' => Yii::t('app', 'Method of Recruitment to Current Service'),
            'w_op_number' => Yii::t('app', 'W and OP Number'),
            'appointment_status' => Yii::t('app', 'Status of Appointment'),
            'district' => Yii::t('app', 'District'),
            'division' => Yii::t('app', 'Division'),
            'signature' => Yii::t('app', 'Signature'),
            'profile_image' => Yii::t('app', 'Profile Image'),
            'agreement' => Yii::t('app', 'Agreement'),
            'status' => Yii::t('app', 'Status'),
            'user_level' => Yii::t('app', 'User Rank'),
            'device_serial' => Yii::t('app', 'Device Serial Number (If availble)'),
            'contact_number' => Yii::t('app', 'Contact Number'),
            'it_result_sheet' => Yii::t('app', 'It Result Sheet'),
            'cetificate' => Yii::t('app', 'Cetificate'),
            'privacy_policy' => Yii::t('app', 'Privacy Policy'),

        ];
    }

    /**
     * Gets query for [[District0]].
     *
     * @return ActiveQuery
     */
    public function getDistrict0()
    {
        return $this->hasOne(MFiDistrict::class, ['id' => 'district']);
    }

    /**
     * Gets query for [[Division0]].
     *
     * @return ActiveQuery
     */
    public function getDivision0()
    {
        return $this->hasOne(MDivision::class, ['id' => 'division']);
    }
    /**
     * {@inheritdoc}
     */
    public static function primaryKey()
    {
        return ['id'];
    }
}