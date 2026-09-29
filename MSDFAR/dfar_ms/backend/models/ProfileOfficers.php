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
 * @property string|null $date_of_birth
 * @property string|null $place_of_birth
 * @property string|null $permanent_address
 * @property int|null $district
 * @property int|null $division
 * @property string|null $harbour
 * @property string|null $signature
 * @property string|null $passport_copy
 * @property string|null $driving_license_copy
 * @property string|null $profile_image
 * @property string|null $agreement
 * @property int $status
 * @property int $force_reset_pw
 * @property string $user_level
 * @property string|null $device_serial
 * @property string|null $mobile_phone
 * @property string|null $home_phone
 * @property string|null $personal_email
 * @property string|null $photograph
 * @property string|null $it_result_sheet
 * @property string|null $cetificate
 * @property string|null $ministry_dept
 */
class ProfileOfficers extends ActiveRecord
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
            [['first_name', 'last_name', 'status'], 'required'],
            // Minimum profile-completion fields (gate enforces these too):
            [['mobile_phone'], 'required'],
            [['personal_email'], 'required'],
            [['personal_email'], 'email'],
            [['personal_email'], 'string', 'max' => 200],
            ['district', 'required', 'when' => function ($model) {
                // District required for everyone except DG and DM.
                return !UserTypeUtil::hasType(Constant::DG) && !UserTypeUtil::hasType(Constant::DM) && !UserTypeUtil::hasType(Constant::DIRECTOR) && !UserTypeUtil::hasType(Constant::ICT_OFFICER) && !UserTypeUtil::hasType(Constant::FISHERIES_OFFICER)  && !UserTypeUtil::hasType(Constant::DEVELOPMENT_OFFICER)  && !UserTypeUtil::hasType(Constant::MANAGEMENT_SERVICE_OFFICER) && !UserTypeUtil::hasType(Constant::CC) && !UserTypeUtil::hasType(Constant::KKS) && !UserTypeUtil::hasType(Constant::REPORT_OFFICER);

            }, 'enableClientValidation' => false],
            [['public_service_appointment_date', 'dfar_appointment_date'], 'safe'],
            [['district', 'division', 'status', 'force_reset_pw'], 'integer'],
            [['first_name', 'last_name', 'appointment_letter', 'dfar_appointment_letter'], 'string', 'max' =>
                200],
            [['nic'], 'string', 'max' => 20],
            [['current_designation', 'current_workplace', 'district_office', 'recruitment_method', 'place_of_birth', 'harbour', 'device_serial'], 'string', 'max' => 100],
            [['ministry_dept'], 'string', 'max' => 255],
            [['current_workplace_type', 'w_op_number', 'appointment_status', 'user_level'], 'string', 'max' => 50],
            [['mobile_phone', 'home_phone'], 'string', 'max' => 10],
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
            'current_workplace' => Yii::t('app', 'Current Workplace'),
            'district_office' => Yii::t('app', 'District Office'),
            'public_service_appointment_date' => Yii::t('app', 'Public Service Appointment Date'),
            'appointment_letter' => Yii::t('app', 'Appointment Letter'),
            'dfar_appointment_date' => Yii::t('app', 'DFAR Appointment Date'),
            'dfar_appointment_letter' => Yii::t('app', 'DFAR Appointment Letter'),
            'recruitment_method' => Yii::t('app', 'Method of Recruitment to Current Service'),
            'w_op_number' => Yii::t('app', 'W and OP Number'),
            'appointment_status' => Yii::t('app', 'Status of Appointment'),
            'date_of_birth' => Yii::t('app', 'Date Of Birth'),
            'place_of_birth' => Yii::t('app', 'Place Of Birth'),
            'permanent_address' => Yii::t('app', 'Permanent Address'),
            'district' => Yii::t('app', 'District (If applicable)'),
            'division' => Yii::t('app', 'Division (If applicable)'),
            'harbour' => Yii::t('app', 'Harbour (If applicable)'),
            'signature' => Yii::t('app', 'Signature'),
            'passport_copy' => Yii::t('app', 'Passport Copy'),
            'driving_license_copy' => Yii::t('app', 'Driving License Copy'),
            'profile_image' => Yii::t('app', 'Profile Image'),
            'agreement' => Yii::t('app', 'Agreement'),
            'status' => Yii::t('app', 'Status'),
            'user_level' => Yii::t('app', 'User Level'),
            'device_serial' => Yii::t('app', 'Device Serial'),
            'mobile_phone' => Yii::t('app', 'Mobile Phone'),
            'home_phone' => Yii::t('app', 'Home Phone'),
            'personal_email' => Yii::t('app', 'Personal Email'),
            'photograph' => Yii::t('app', 'Photograph'),
            'it_result_sheet' => Yii::t('app', 'It Result Sheet'),
            'cetificate' => Yii::t('app', 'Certificate'),
        ];
    }

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
}