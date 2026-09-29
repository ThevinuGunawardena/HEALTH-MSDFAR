<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "submission_audit_log".
 *
 * Shared audit trail for BSC-1 and BSC-2. Every field change, submit,
 * validate, return, and reopen action is logged — including whether it
 * happened via admin impersonation, per
 * common\components\WebUser::getIsImpersonated()/getMainIdentityId().
 *
 * @property int $id
 * @property string $form_type bsc1, bsc2
 * @property int $submission_id
 * @property string $action create, update, submit, validate, return, reopen
 * @property string|null $field_changed
 * @property string|null $old_value
 * @property string|null $new_value
 * @property int $changed_by
 * @property int $acted_as_admin
 * @property int|null $real_actor
 * @property string $changed_at
 */
class SubmissionAuditLogBscForms extends ActiveRecord
{
    const FORM_TYPES = ['bsc1', 'bsc2'];
    const ACTIONS = ['create', 'update', 'submit', 'validate', 'return', 'reopen'];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'submission_audit_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['form_type', 'submission_id', 'action', 'changed_by'], 'required'],
            [['form_type'], 'in', 'range' => self::FORM_TYPES],
            [['action'], 'in', 'range' => self::ACTIONS],
            [['submission_id', 'changed_by', 'real_actor'], 'integer'],
            [['field_changed'], 'string', 'max' => 100],
            [['old_value', 'new_value'], 'string', 'max' => 255],
            [['acted_as_admin'], 'boolean'],
            [['acted_as_admin'], 'default', 'value' => 0],
            [['changed_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'form_type' => Yii::t('app', 'Form'),
            'submission_id' => Yii::t('app', 'Submission'),
            'action' => Yii::t('app', 'Action'),
            'field_changed' => Yii::t('app', 'Field Changed'),
            'old_value' => Yii::t('app', 'Old Value'),
            'new_value' => Yii::t('app', 'New Value'),
            'changed_by' => Yii::t('app', 'Changed By'),
            'acted_as_admin' => Yii::t('app', 'Via Impersonation'),
            'real_actor' => Yii::t('app', 'Real Actor'),
            'changed_at' => Yii::t('app', 'Changed At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChangedByUser()
    {
        return $this->hasOne(\common\models\User::class, ['id' => 'changed_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRealActorUser()
    {
        return $this->hasOne(\common\models\User::class, ['id' => 'real_actor']);
    }
}