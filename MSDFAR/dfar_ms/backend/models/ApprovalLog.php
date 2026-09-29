<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "approval_log".
 *
 * @property int $id
 * @property string $type
 * @property int $process_id
 * @property int $done_by
 * @property string $status
 * @property string $remark
 * @property string $date_time
 *
 * @property User $doneBy
 */
class ApprovalLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'approval_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'process_id', 'done_by', 'status', 'remark', 'date_time'], 'required'],
            [['process_id', 'done_by'], 'integer'],
            [['date_time'], 'safe'],
            [['type', 'remark'], 'string', 'max' => 200],
            [['status'], 'string', 'max' => 50],
            [['done_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['done_by' => 'id']],
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
            'process_id' => Yii::t('app', 'Process ID'),
            'done_by' => Yii::t('app', 'Done By'),
            'status' => Yii::t('app', 'Status'),
            'remark' => Yii::t('app', 'Remark'),
            'date_time' => Yii::t('app', 'Date Time'),
        ];
    }

    /**
     * Gets query for [[DoneBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDoneBy()
    {
        return $this->hasOne(User::class, ['id' => 'done_by']);
    }
}
