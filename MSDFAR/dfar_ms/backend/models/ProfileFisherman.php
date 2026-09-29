<?php

namespace backend\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "profile_fisherman".
 *
 * @property int $id
 * @property string|null $fisherman_uid
 * @property string $first_name
 * @property string $last_name
 * @property string $preferred_name_for_id
 * @property string $nic
 * @property string $passport
 * @property string $dob
 * @property string $gender
 * @property string $permanent_address
 * @property string $current_address
 * @property string $blood_group
 * @property string $mobile
 * @property string $fixed_line
 * @property string $email
 * @property int $district
 * @property int $division
 * @property int $landing_site
 * @property int $year_recruitment Year of Recruitment
 * @property string|null $life_isurance_no
 * @property int $member_fisheries_society Membership of Fisheries Society
 * @property string $civil
 * @property int|null $category
 * @property int $management_area
 * @property string|null $profile_image
 * @property string|null $signature
 * @property int $status
 * @property string|null $approval_stage
 * @property string $created
 * @property string|null $approved_time
 * @property string|null $expire_date
 * @property int $renew
 * @property int|null $renew
 * @property string|null $printed_date
 * @property int $privacy_policy

 * @property BoatNumbers[] $boatNumbers
 * @property FishermanRegisterdBoat[] $boatRegistrations
 * @property MFiDistrict $district0
 * @property MDivision $division0
 * @property MFishermanCategory $category0
 * @property FishermanDependant[] $fishermanDependants
 * @property FishermanRegisterdBoat[] $fishermanRegisterdBoats
 * @property MLandingSite $landingSite
 * @property NationalLicense[] $nationalLicenses
 * @property ProfileYard[] $profileYards
 * @property Skipper $skipper
 */
class ProfileFisherman extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'profile_fisherman';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'preferred_name_for_id', 'nic', 'dob', 'gender', 'permanent_address', 'current_address', 'mobile', 'district', 'division', 'year_recruitment', 'member_fisheries_society', 'civil', 'management_area', 'category','privacy_policy'], 'required'],
            [['dob', 'created', 'approved_time', 'expire_date', 'printed_date', 'email', 'fixed_line', 'blood_group', 'name_sinhala', 'name_tamil', 'address_sinhala', 'address_tamil'], 'safe'],
            [['district', 'division', 'landing_site', 'year_recruitment', 'member_fisheries_society', 'category', 'management_area', 'status', 'printed', 'privacy_policy','renew','renew_id'], 'integer'],
            [['fisherman_uid', 'nic', 'passport', 'profile_image', 'signature'], 'string', 'max' => 100],
            [['first_name', 'last_name', 'preferred_name_for_id', 'email', 'life_isurance_no'], 'string', 'max' => 200],
            [['gender'], 'string', 'max' => 20],
            [['permanent_address', 'current_address'], 'string', 'max' => 500],
            [['blood_group', 'mobile', 'fixed_line'], 'string', 'max' => 15],
            [['civil'], 'string', 'max' => 10],
            [['approval_stage'], 'string', 'max' => 11],
            [['nic'], 'unique'],
            [['fisherman_uid'], 'unique'],
            [['division'], 'exist', 'skipOnError' => true, 'targetClass' => MDivision::class, 'targetAttribute' => ['division' => 'id']],
            [['district'], 'exist', 'skipOnError' => true, 'targetClass' => MFiDistrict::class, 'targetAttribute' => ['district' => 'id']],
            [['landing_site'], 'exist', 'skipOnError' => true, 'targetClass' => MLandingSite::class, 'targetAttribute' => ['landing_site' => 'id']],
            [
                'first_name', 'match',
                'pattern' => '/^[a-zA-Z\s]+$/',
                'message' => 'Cannot contain numbers or special characters.'
            ],
            [
                'last_name', 'match',
                'pattern' => '/^[a-zA-Z\s]+$/',
                'message' => 'Cannot contain numbers or special characters.'
            ],
            [
                'preferred_name_for_id', 'match',
                'pattern' => '/^[a-zA-Z\s.]+$/',
                'message' => 'Cannot contain numbers or special characters.'
            ],
            [
                'nic', 'match',
                'pattern' => '/^\d{9}[Vv]$|^\d{12}$/',
                'message' => 'Invalid NIC number format. Please enter in 123456789V or 123456789012 format.'
            ],
            [['privacy_policy'], 'compare', 'compareValue' => 1, 'message' => 'You must agree to the Privacy Policy.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Fisherman'),
            'fisherman_uid' => Yii::t('app', 'Fisherman Uid'),
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'preferred_name_for_id' => Yii::t('app', 'Name to be printed in certificate'),
            'nic' => Yii::t('app', 'Nic'),
            'passport' => Yii::t('app', 'Passport'),
            'dob' => Yii::t('app', 'Date of birth'),
            'gender' => Yii::t('app', 'Gender'),
            'permanent_address' => Yii::t('app', 'Permanent Address'),
            'current_address' => Yii::t('app', 'Current Address'),
            'blood_group' => Yii::t('app', 'Blood Group'),
            'mobile' => Yii::t('app', 'Mobile'),
            'fixed_line' => Yii::t('app', 'Fixed Line'),
            'email' => Yii::t('app', 'Email'),
            'district' => Yii::t('app', 'District'),
            'division' => Yii::t('app', 'Division'),
            'landing_site' => Yii::t('app', 'Landing Site'),
            'year_recruitment' => Yii::t('app', 'Year Recruitment'),
            'life_isurance_no' => Yii::t('app', 'Life Isurance No'),
            'member_fisheries_society' => Yii::t('app', 'Member Fisheries Society'),
            'civil' => Yii::t('app', 'Civil'),
            'category' => Yii::t('app', 'Category'),
            'management_area' => Yii::t('app', 'Management Area'),
            'profile_image' => Yii::t('app', 'Profile Image'),
            'signature' => Yii::t('app', 'Signature'),
            'status' => Yii::t('app', 'Status'),
            'approval_stage' => Yii::t('app', 'Approval Stage'),
            'created' => Yii::t('app', 'Created'),
            'approved_time' => Yii::t('app', 'Approved Time'),
            'expire_date' => Yii::t('app', 'Expire Date'),
        ];
    }

    /**
     * Gets query for [[BoatNumbers]].
     *
     * @return ActiveQuery
     */
    public function getBoatNumbers()
    {
        return $this->hasMany(BoatNumbers::class, ['owner' => 'id']);
    }

    /**
     * Gets query for [[BoatRegistrations]].
     *
     * @return ActiveQuery
     */
    public function getBoatRegistrations()
    {
        return $this->hasMany(FishermanRegisterdBoat::class, ['id' => 'boat_registration_id'])->viaTable('national_license', ['fisherman_id' => 'id']);
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
     * Gets query for [[Category0]].
     *
     * @return ActiveQuery
     */
    public function getCategory0()
    {
        return $this->hasOne(MFishermanCategory::class, ['id' => 'category']);
    }

    /**
     * Gets query for [[FishermanDependants]].
     *
     * @return ActiveQuery
     */
    public function getFishermanDependants()
    {
        return $this->hasMany(FishermanDependant::class, ['fisherman_id' => 'id']);
    }

    /**
     * Gets query for [[FishermanRegisterdBoats]].
     *
     * @return ActiveQuery
     */
    public function getFishermanRegisterdBoats()
    {
        return $this->hasMany(FishermanRegisterdBoat::class, ['fisherman_id' => 'id']);
    }

    /**
     * Gets query for [[LandingSite]].
     *
     * @return ActiveQuery
     */
    public function getLandingSite()
    {
        return $this->hasOne(MLandingSite::class, ['id' => 'landing_site']);
    }

    /**
     * Gets query for [[NationalLicenses]].
     *
     * @return ActiveQuery
     */
    public function getNationalLicenses()
    {
        return $this->hasMany(NationalLicense::class, ['fisherman_id' => 'id']);
    }

    /**
     * Gets query for [[ProfileYards]].
     *
     * @return ActiveQuery
     */
    public function getProfileYards()
    {
        return $this->hasMany(ProfileYard::class, ['owner' => 'id']);
    }

    /**
     * Gets query for [[Skipper]].
     *
     * @return ActiveQuery
     */
    public function getSkipper()
    {
        return $this->hasOne(Skipper::class, ['fisherman_id' => 'id']);
    }

    public function getRenewalRecord()
{
    return $this->hasOne(
        ProfileFishermanRenew::class,
        ['id' => 'renew_id']
    );
}
}
