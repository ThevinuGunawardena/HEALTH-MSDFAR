<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "m_requered_documents".
 *
 * @property int $id
 * @property int $type
 * @property string|null $sub_category
 * @property string $discription
 * @property int $status
 *
 * @property MApprovalWorkflow $type0
 */
class MRequeredDocuments extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'm_requered_documents';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'discription'], 'required'],
            [['type', 'status'], 'integer'],
            [['sub_category'], 'string', 'max' => 100],
            [['discription'], 'string', 'max' => 200],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => MApprovalWorkflow::class, 'targetAttribute' => ['type' => 'id']],
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
            'sub_category' => Yii::t('app', 'Sub Category'),
            'discription' => Yii::t('app', 'Discription'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Gets query for [[Type0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType0()
    {
        return $this->hasOne(MApprovalWorkflow::class, ['id' => 'type']);
    }
}
