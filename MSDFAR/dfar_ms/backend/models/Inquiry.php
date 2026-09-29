<?php
namespace backend\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "inquiry".
 *
 * @property int $Inquiry_ID
 * @property int|null $id
 * @property string|null $Name
 * @property string|null $phone_number
 * @property string $Email
 * @property string $Office
 * @property string|null $Inquiry_Type
 * @property string|null $Subject
 * @property string|null $Description
 * @property string|null $Submission_Date
 * @property string|null $Inquiry_Status
 * @property string|null $Response
 * @property string|null $completion_date
 * @property string|null $status
 * @property string|null $remarks
 * @property User $id0
 */
class Inquiry extends \yii\db\ActiveRecord
{
    /**
     * @var UploadedFile|null the uploaded document
     */
    public $uploadedFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inquiry';
    }

    /**
     * {@inheritdoc}
     * Override to include non-database attributes like uploadedFiles
     */
    public function hasAttribute($name)
    {
        if (in_array($name, ['uploadedFile', 'uploadedFiles'])) {
            return true;
        }
        return parent::hasAttribute($name);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'District'], 'integer'],
            [['Email'], 'required'],
            [['Description', 'Response'], 'string'],
            [['Submission_Date', 'completion_date'], 'safe'],
            [['Name', 'Inquiry_Status', 'Office'], 'string', 'max' => 50],
            [['phone_number'], 'string', 'max' => 12],
            [['Email', 'Subject'], 'string', 'max' => 255],
            [['Inquiry_Type'], 'string', 'max' => 100],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['id' => 'id']],

            [['remarks'], 'string'],
            [
                ['uploadedFile'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => 'pdf,doc,docx,xlsx,jpg,jpeg,png,mp3',
                'maxSize' => 10 * 1024 * 1024,
                'tooBig' => 'File size must not exceed 10MB'
            ],
            [
                ['uploadedFiles'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => 'pdf,doc,docx,jpg,jpeg,png,mp3',
                'maxSize' => 10 * 1024 * 1024,
                'tooBig' => 'File size must not exceed 10MB',
                'maxFiles' => 5,
                'tooMany' => 'You can upload maximum 5 files'
            ],
            [['ip_address'], 'string', 'max' => 45],
            [['file_type', 'file_status'], 'integer'],
            [['file_name'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Inquiry_ID' => Yii::t('app', 'Inquiry ID'),
            'id' => Yii::t('app', 'ID'),
            'Name' => Yii::t('app', 'Name'),
            'phone_number' => Yii::t('app', 'Phone Number'),
            'Email' => Yii::t('app', 'Email'),
            'District' => Yii::t('app', 'District'),
            'Office' => Yii::t('app', 'Office'),
            'Inquiry_Type' => Yii::t('app', 'Inquiry Type'),
            'Subject' => Yii::t('app', 'Subject'),
            'Description' => Yii::t('app', 'Description'),
            'Submission_Date' => Yii::t('app', 'Submission Date & Time'),
            'Inquiry_Status' => Yii::t('app', 'Status'),
            'Response' => Yii::t('app', 'Response'),
            'completion_date' => Yii::t('app', 'Completion Date & Time'),
            'status' => Yii::t('app', 'Status'),
            'remarks' => Yii::t('app', 'Remarks'),
            'ip_address' => Yii::t('app', 'IP Address'),
        ];
    }

    /**
     * Gets query for [[Id0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getId0()
    {
        return $this->hasOne(User::class, ['id' => 'id']);
    }

    /**
     * Gets query for [[District0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDistrict0()
    {
        return $this->hasOne(\backend\models\MFiDistrict::class, ['id' => 'District']);
    }

    /**
     * Sets the submission date before saving a new record.
     * Sets completion date when status is 'Completed'.
     *
     * @param bool $insert whether this method is called while inserting a new record.
     * @return bool whether the saving should proceed.
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                // Set the Submission_Date to the current date and time for new records
                $this->Submission_Date = date('Y-m-d H:i:s');
            }
            // Set completion_date when status is 'Completed' and it's not already set
            if ($this->Inquiry_Status === 'Completed' && empty($this->completion_date)) {
                $this->completion_date = date('Y-m-d H:i:s');
            }
            return true;
        }
        return false;
    }


    /**
     * Get file relation
     * @return \yii\db\ActiveQuery
     */
    public function getFile()
    {
        return $this->hasOne(Files::class, ['process_id' => 'Inquiry_ID'])
            ->andWhere(['type' => 'INQUIRY'])
            ->andWhere(['status' => 1]);
    }

    /**
     * Check if inquiry has an attached file
     * @return bool
     */
    public function hasFile()
    {
        return $this->file !== null;
    }

    /**
     * Get file URL
     * @return string|null
     */
    public function getFileUrl()
    {
        if ($this->file && $this->file->file_name) {
            $filePath = Yii::getAlias('@web/uploads/files/inquiry/') . $this->file->file_name;
            return $filePath;
        }
        return null;
    }
}