<?php

namespace backend\models;

use Yii;

class Bsc2SubmissionExtension extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'bsc2_submission_extensions';
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

    public function getFiDivision()
    {
        return $this->hasOne(
            MDivision::class,
            ['id' => 'fi_division_id']
        );
    }

    public function getGrantedBy()
    {
        return $this->hasOne(
            User::class,
            ['id' => 'granted_by']
        );
    }
}