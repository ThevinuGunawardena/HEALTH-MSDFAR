<?php

namespace backend\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "skipper_re_correction".
 *
 * @property int $id
 * @property int $skipper_id
 * @property string|null $skipper_uid
 * @property string|null $nic
 * @property int|null $main_id
 * @property string|null $division
 * @property string|null $district
 * @property string $updated_at
 *
 * @property Skipper $skipper
 */
class SkipperReCorrection extends ActiveRecord
{
    public static function tableName()
    {
        return 'skipper_re_correction';
    }

    public function rules()
    {
        return [
            [['skipper_id'], 'required'],
            [['skipper_id', 'main_id'], 'integer'],
            [['skipper_uid'], 'string', 'max' => 100],
            [['nic'], 'string', 'max' => 20],
            [['division', 'district'], 'integer'],
            [['updated_at'], 'safe'],
            [['skipper_id'], 'exist', 'skipOnError' => true, 'targetClass' => Skipper::class, 'targetAttribute' => ['skipper_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'skipper_id' => Yii::t('app', 'Skipper ID'),
            'skipper_uid' => Yii::t('app', 'Skipper UID'),
            'nic' => Yii::t('app', 'NIC'),
            'main_id' => Yii::t('app', 'Main ID'),
            'division' => Yii::t('app', 'Division'),
            'district' => Yii::t('app', 'District'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function getSkipper()
    {
        return $this->hasOne(Skipper::class, ['id' => 'skipper_id']);
    }
}