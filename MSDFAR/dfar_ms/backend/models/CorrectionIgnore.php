<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "correction_ignore".
 *
 * @property int $id
 * @property int $main_id
 * @property string $ignore_type
 * @property int|null $division
 * @property int|null $district
 * @property string $updated_at
 */
class CorrectionIgnore extends ActiveRecord
{
    // Ignore type constants
    const Fisherman = 'Fisherman';
    const Skipper   = 'Skipper';
    const TYPE_OTHER = 'other';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'correction_ignore';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'updated_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['main_id', 'ignore_type'], 'required'],
            [['main_id', 'division', 'district'], 'integer'],
            [['ignore_type'], 'string', 'max' => 20],
            [['ignore_type'], 'in', 'range' => array_keys(self::ignoreTypes())],
            [['updated_at'], 'safe'],
        ];
    }

    /**
     * Map of ignore_type value => human readable label.
     */
    public static function ignoreTypes()
    {
        return [
            self::Fisherman  => 'Fisherman',
            self::Skipper    => 'Skipper',
            self::TYPE_OTHER => 'Other',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'main_id'     => 'Main ID',
            'ignore_type' => 'Ignore Type',
            'division'    => 'Division',
            'district'    => 'District',
            'updated_at'  => 'Updated At',
        ];
    }
}