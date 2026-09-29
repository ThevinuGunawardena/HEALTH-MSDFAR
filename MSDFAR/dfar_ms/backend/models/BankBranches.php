<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "bank_branches".
 *
 * @property int $id
 * @property string $branch_name
 * @property int $branch_code
 * @property int $bank_code
 */
class BankBranches extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bank_branches';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['branch_name', 'branch_code', 'bank_code'], 'required'],
            [['branch_code', 'bank_code'], 'integer'],
            [['branch_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'branch_name' => Yii::t('app', 'Branch Name'),
            'branch_code' => Yii::t('app', 'Branch Code'),
            'bank_code' => Yii::t('app', 'Bank Code'),
        ];
    }

}
