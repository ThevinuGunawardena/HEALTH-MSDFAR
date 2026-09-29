<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "departure_requests".
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
class DepartureRequests extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'departure_requests';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['agree', 'contact_no'], 'required'],
            [['boat_name'], 'required', 'message' => 'යාත්‍රාවේ නම හිස්ව තැබිය නොහැක. | Boat Name cannot be blank. | படகின் பெயர் வெற்றிடமாக இருத்தலாகாது.'],
            [['boat_no'], 'required', 'message' => 'යාත්‍රාවේ ලියාපදිංචි අංකය හිස්ව තැබිය නොහැක.| Boat Registration Number cannot be blank.| படகின் பதிவு எண் வெற்றிடமாக இருத்தலாகாது.'],
            [['skipper_nic'], 'required', 'message' => 'නියමුවාගේ ජාතික හැදුනුම්පත් අංකය හිස්ව තැබිය නොහැක. | Skipper`s National Identity Card No cannot be blank. | படகோட்டியின் தேசிய அடையாள அட்டை இலக்கம் வெற்றிடமாக இருத்தலாகாது.'],
            [['skipper_no'], 'required', 'message' => 'නියමු අංකය හිස්ව තැබිය නොහැක. | Skipper License Number cannot be blank. | படகோட்டி உரிம எண் வெற்றிடமாக இருத்தலாகாது.'],
            [['skipper'], 'required', 'message' => 'ඔබේ නියමුවාගේ නම හිස්ව තැබිය නොහැක. | Skipper Name cannot be blank. | படகோட்டியின் பெயர் வெற்றிடமாக இருத்தலாகாது.'],
            [['harbor'], 'required', 'message' => 'බෝට්ටුව පිටත්වෙන්න බලාපොරොත්තුවන වරාය හිස්ව තැබිය නොහැක. | Departure Port cannot be blank. | புறப்படு துறைமுகம் வெற்றிடமாக இருத்தலாகாது.'],
            [['fishing_area'], 'required', 'message' => 'ධීවර මෙහෙයුම අතරදී මසුන් බාන ප්‍රදේශය හිස්ව තැබිය නොහැක. | Area of Fishing Operation cannot be blank. | மீன்பிடி செயல்பாட்டுப் பகுதி வெற்றிடமாக இருத்தலாகாது.'],
            [['vms'], 'required', 'message' => 'යාත්‍රාවේ VMS උපකරණයක් තිබේද යන්න සහ එය ක්‍රියාත්මක තත්වයේ තිබේද යන්න හිස්ව තැබිය නොහැක. | VMS on board cannot be blank. | படகில் VMS கருவி உள்ளதா மற்றும் அது செயல்பாட்டில் உள்ளதா என்பவை வெற்றிடமாக இருத்தலாகாது.'],
            [['mcs'], 'required', 'message' => 'SSB රේඩියෝ යන්ත්‍රය තිබේද යන්න හිස්ව තැබිය නොහැක. | SSB radio cannot be blank. | SSB வானொலி வெற்றிடமாக இருத்தலாகாது.'],

            [['length_longline', 'length_gillnet', 'length_ringnet', 'mesh_gillnet', 'mesh_ringnet'], 'number'],
            [['longline_hooks'], 'integer'],
            [['req_date_time', 'action_date'], 'safe'],
            [['boat_no'], 'string', 'max' => 20],
            [['boat_name', 'vms_code', 'arrivalPort', 'arrivalDate', 'arrTime'], 'string', 'max' => 80],
            [['owner', 'email', 'skipper', 'district', 'user', 'remarks', 'mcs', 'frequency'], 'string', 'max' => 225],
            [['contact_no', 'skipper_nic', 'water_bot'], 'string', 'max' => 15],
            [['skipper_no'], 'string', 'max' => 50],
            [['harbor'], 'string', 'max' => 100],
            [['fishing_area', 'national_license_no', 'hs_license_no'], 'string', 'max' => 25],
            [['vms', 'agree', 'approve'], 'string', 'max' => 10],
            [['manual'], 'string', 'max' => 6],
            [['agree'], 'required', 'requiredValue' => 1, 'message' => 'Please accept the terms and conditions.'],
            [['email'], 'required', 'message' => 'Please contact Harbour officer to update the email | කරුණාකර ඉමේල් යාවත්කාලීන කිරීමට වරාය නිලධාරියා අමතන්න. | மின்னஞ்சலைப் புதுப்பிக்க துறைமுக அதிகாரியை தொடர்பு கொள்ளவும்.']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Departure No'),
            'boat_no' => Yii::t('app', 'Boat Number '),
            'boat_name' => Yii::t('app', 'Boat Name'),
            'owner' => Yii::t('app', 'Name of the Owner'),
            'contact_no' => Yii::t('app', 'Owner`s Contact No '),
            'email' => Yii::t('app', 'Owner`s Email'),
            'skipper' => Yii::t('app', 'Skipper Name'),
            'skipper_no' => Yii::t('app', 'Skipper License Number'),
            'skipper_nic' => Yii::t('app', 'Skipper`s NIC No'),
            'district' => Yii::t('app', 'District'),
            'harbor' => Yii::t('app', 'Departure Port '),
            'fishing_area' => Yii::t('app', 'Area of Fishing Operation'),
            'length_longline' => Yii::t('app', 'Length of Long Line (m)'),
            'length_gillnet' => Yii::t('app', 'Length of Gill Net (m)'),
            'length_ringnet' => Yii::t('app', 'Length of Purse Seine (km)'),
            'longline_hooks' => Yii::t('app', 'No. of Hooks in Long Line'),
            'mesh_gillnet' => Yii::t('app', 'Mesh Size in Gill Net (inch) '),
            'mesh_ringnet' => Yii::t('app', 'Mesh Size in Purse Seine (inch) '),
            'national_license_no' => Yii::t('app', 'National License No'),
            'hs_license_no' => Yii::t('app', 'High Seas License No'),
            'vms' => Yii::t('app', 'VMS on board'),
            'agree' => Yii::t('app', 'එකගවෙමි.  (එකගවිමට මෙහි ඔබන්න) | சம்மதிக்கிறேன்.(சம்மதத்திற்கு இங்கே கிளிக் செய்க | I agree.  (Click here to agree))'),
            'req_date_time' => Yii::t('app', 'Req Date Time'),
            'user' => Yii::t('app', 'Approved by'),
            'action_date' => Yii::t('app', 'Approved Date'),
            'approve' => Yii::t('app', 'Status'),
            'remarks' => Yii::t('app', 'Remarks'),
            'water_bot' => Yii::t('app', 'Water Bot'),
            'mcs' => Yii::t('app', 'SSB Radio on board'),
            'frequency' => Yii::t('app', 'Frequency'),
            'vms_code' => Yii::t('app', 'Active VMS Code'),
            'manual' => Yii::t('app', 'Manual'),
            'arrivalPort' => Yii::t('app', 'Arrival Port'),
            'arrivalDate' => Yii::t('app', 'Arrival Date'),
            'arrTime' => Yii::t('app', 'Arr Time'),
        ];
    }
}
