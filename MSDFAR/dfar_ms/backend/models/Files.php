<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "files".
 *
 * @property int $id
 * @property string $type
 * @property int $file_type
 * @property string $file_name
 * @property int $process_id
 * @property int $status
 *
 * @property MRequeredDocuments $fileType
 */
class Files extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'file_type', 'file_name', 'process_id', 'status'], 'required'],
            [['file_type', 'process_id', 'status'], 'integer'],
            [['type'], 'string', 'max' => 100],
            [['file_name'], 'string', 'max' => 200],
            [['file_type'], 'exist', 'skipOnError' => true, 'targetClass' => MRequeredDocuments::class, 'targetAttribute' => ['file_type' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'file_type' => Yii::t('app', 'File Type'),
            'file_name' => Yii::t('app', 'File Name'),
            'process_id' => Yii::t('app', 'Process ID'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Gets query for [[FileType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFileType()
    {
        return $this->hasOne(MRequeredDocuments::class, ['id' => 'file_type']);
    }
}
