<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "profile_fisherman_renew".
 *
 * @property int $id
 * @property string|null $fisherman_uid
 * @property int $fisherman_id
 * @property int $district
 * @property int $division
 * @property int $status
 * @property string|null $approval_stage
 * @property string $created
 * @property string|null $approved_time
 * @property string|null $expire_date
 * @property int|null $renew
 * @property int $printed
 * @property string|null $printed_date
 * @property int $privacy_policy
 */
class ProfileFishermanRenew extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'profile_fisherman_renew';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fisherman_uid', 'approval_stage', 'approved_time', 'expire_date', 'printed_date'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 1],
            [['printed'], 'default', 'value' => 0],
            [['fisherman_id', 'district', 'division', 'privacy_policy'], 'required'],
            [['id', 'fisherman_id', 'district', 'division', 'status', 'renew', 'printed', 'privacy_policy'], 'integer'],
            [['created', 'approved_time', 'expire_date', 'printed_date'], 'safe'],
            [['fisherman_uid'], 'string', 'max' => 100],
            [['approval_stage'], 'string', 'max' => 11],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'fisherman_uid' => Yii::t('app', 'Fisherman Uid'),
            'fisherman_id' => Yii::t('app', 'Fisherman ID'),
            'district' => Yii::t('app', 'District'),
            'division' => Yii::t('app', 'Division'),
            'status' => Yii::t('app', 'Status'),
            'approval_stage' => Yii::t('app', 'Approval Stage'),
            'created' => Yii::t('app', 'Created'),
            'approved_time' => Yii::t('app', 'Approved Time'),
            'expire_date' => Yii::t('app', 'Expire Date'),
            'renew' => Yii::t('app', 'Renew'),
            'printed' => Yii::t('app', 'Printed'),
            'printed_date' => Yii::t('app', 'Printed Date'),
            'privacy_policy' => Yii::t('app', 'Privacy Policy'),
        ];
    }

}
