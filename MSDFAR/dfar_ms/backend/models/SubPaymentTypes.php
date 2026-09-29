<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "sub_payment_types".
 *
 * @property int $TypeID
 * @property int $payment_type_id
 * @property string $Description
 * @property float $Amount
 * @property string|null $Code
 * @property string|null $extra_feilds
 *
 * @property Paymenttypes $paymentType
 */
class SubPaymentTypes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sub_payment_types';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payment_type_id', 'Description', 'Amount'], 'required'],
            [['payment_type_id'], 'integer'],
            [['Amount'], 'number'],
            [['Description', 'Code', 'extra_feilds'], 'string', 'max' => 255],
            [['payment_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => Paymenttypes::class, 'targetAttribute' => ['payment_type_id' => 'payment_type_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'TypeID' => Yii::t('app', 'Type ID'),
            'payment_type_id' => Yii::t('app', 'Payment Type ID'),
            'Description' => Yii::t('app', 'Description'),
            'Amount' => Yii::t('app', 'Amount'),
            'Code' => Yii::t('app', 'Code'),
            'extra_feilds' => Yii::t('app', 'Extra Feilds'),
        ];
    }

    /**
     * Gets query for [[PaymentType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentType()
    {
        return $this->hasOne(Paymenttypes::class, ['payment_type_id' => 'payment_type_id']);
    }
}
