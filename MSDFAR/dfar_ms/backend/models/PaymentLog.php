<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "payment_log".
 *
 * @property int $id
 * @property string $type
 * @property float $amount
 * @property int $process_id
 * @property string $ref
 * @property string|null $file
 * @property int $status
 */
class PaymentLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'amount', 'process_id', 'ref', 'status','file'], 'required'],
            [['amount'], 'number'],
            [['process_id', 'status'], 'integer'],
            [['type', 'ref'], 'string', 'max' => 100],
            [['file'], 'string', 'max' => 50],
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
            'amount' => Yii::t('app', 'Amount'),
            'process_id' => Yii::t('app', 'Process ID'),
            'ref' => Yii::t('app', 'Ref'),
            'file' => Yii::t('app', 'File'),
            'status' => Yii::t('app', 'Status'),
        ];
    }
}
