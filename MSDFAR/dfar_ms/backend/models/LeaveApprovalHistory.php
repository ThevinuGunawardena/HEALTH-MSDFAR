<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "leave_approval_history".
 *
 * @property int         $id
 * @property int         $leave_id
 * @property int         $done_by
 * @property string      $status
 * @property string|null $remark
 * @property string      $date_time
 */
class LeaveApprovalHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'leave_approval_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['leave_id', 'done_by', 'status'], 'required'],
            [['leave_id', 'done_by'],            'integer'],
            [['remark'],                         'string'],
            [['date_time'],                      'safe'],
            [['status'],                         'string', 'max' => 20],
            [['status'],                         'in', 'range' => ['approve', 'reject']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'        => Yii::t('app', 'ID'),
            'leave_id'  => Yii::t('app', 'Leave'),
            'done_by'   => Yii::t('app', 'Done By'),
            'status'    => Yii::t('app', 'Status'),
            'remark'    => Yii::t('app', 'Remark'),
            'date_time' => Yii::t('app', 'Date / Time'),
        ];
    }

    // ---------------------------------------------------------------
    // Relations
    // ---------------------------------------------------------------

    /** The leave record this history entry belongs to */
    public function getLeave()
    {
        return $this->hasOne(Leave::class, ['id' => 'leave_id']);
    }

    /** The user who performed the action */
    public function getDoneBy()
    {
        return $this->hasOne(\common\models\User::class, ['id' => 'done_by']);
    }

    // ---------------------------------------------------------------
    // Lifecycle hooks
    // ---------------------------------------------------------------

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                // Use Sri Lanka timezone explicitly
                $this->date_time = (new \DateTime('now', new \DateTimeZone('Asia/Colombo')))
                    ->format('Y-m-d H:i:s');
            }
            return true;
        }
        return false;
    }

    // ---------------------------------------------------------------
    // Helper — create a history entry in one line
    // ---------------------------------------------------------------

    /**
     * Records an approval action into leave_approval_history.
     *
     * Usage in controller:
     *   LeaveApprovalHistory::record($model->id, Yii::$app->user->id, 'approve', 'Looks good');
     *
     * @param int         $leaveId
     * @param int         $doneBy
     * @param string      $status   'approve' or 'reject'
     * @param string|null $remark
     * @return bool
     */
    public static function record($leaveId, $doneBy, $status, $remark = null)
    {
        $history            = new self();
        $history->leave_id  = $leaveId;
        $history->done_by   = $doneBy;
        $history->status    = $status;
        $history->remark    = $remark;
        return $history->save(false);
    }
}