<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "m_approval_workflow".
 *
 * @property int $id
 * @property string $type
 * @property string $workflow
 * @property int $expired_in number of months
 * @property int $payment_requred
 * @property int $file_upload
 * @property string $requred_documents
 * @property int $status
 */
class MApprovalWorkflow extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'm_approval_workflow';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'workflow', 'requred_documents', 'status'], 'required'],
            [['expired_in', 'payment_requred', 'file_upload', 'status'], 'integer'],
            [['type', 'workflow', 'requred_documents'], 'string', 'max' => 200],
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
            'workflow' => Yii::t('app', 'Workflow'),
            'expired_in' => Yii::t('app', 'Expired In'),
            'payment_requred' => Yii::t('app', 'Payment Requred'),
            'file_upload' => Yii::t('app', 'File Upload'),
            'requred_documents' => Yii::t('app', 'Requred Documents'),
            'status' => Yii::t('app', 'Status'),
        ];
    }
}
