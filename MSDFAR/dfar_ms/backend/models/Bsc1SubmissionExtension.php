<?php

namespace backend\models;

use Yii;

/**
 * This model represents the 48-hour extension granted
 * to an FI for a BSC-1 reporting period.
 *
 * @property int $id
 * @property int $fi_division_id
 * @property string $period_date
 * @property int $granted_by
 * @property string $granted_at
 * @property string $extension_deadline
 * @property string $created_at
 * @property string|null $updated_at
 *
 * @property MDivision $fiDivision
 * @property User $grantedBy
 */
class Bsc1SubmissionExtension extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'bsc1_submission_extensions';
    }

    public function rules()
    {
        return [
            [['fi_division_id', 'granted_by'], 'integer'],
            
            [
                [
                    'period_date',
                    'granted_at',
                    'extension_deadline',
                    'created_at',
                    'updated_at'
                ],
                'safe'
            ],

            [
                [
                    'fi_division_id',
                    'period_date',
                    'granted_by',
                    'granted_at',
                    'extension_deadline',
                    'created_at'
                ],
                'required'
            ],
        ];
    }

    /**
     * The division that receives the extension.
     */
    public function getFiDivision()
    {
        return $this->hasOne(MDivision::class, ['id' => 'fi_division_id']);
    }

    /**
     * The AD who granted the extension.
     */
    public function getGrantedBy()
    {
        return $this->hasOne(User::class, ['id' => 'granted_by']);
    }
}