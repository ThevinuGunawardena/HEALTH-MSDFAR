<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "boat_number_cancel_requests".
 *
 * @property int $id
 * @property int $boat_number_id
 * @property int $repairable
 * @property int $parts_available_for_inspection
 * @property string|null $proposed_dispose
 * @property string|null $address_of_part_inspection
 * @property string|null $present_condition_of_boat
 * @property int $status
 * @property string $approval_stage
 * @property string $created
 * @property string|null $approved_time
 *
 * @property BoatNumbers $boatNumber
 */
class BoatNumberCancelRequests extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'boat_number_cancel_requests';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['boat_number_id', 'repairable', 'parts_available_for_inspection', 'approval_stage'], 'required'],
            [['boat_number_id', 'repairable', 'parts_available_for_inspection', 'status'], 'integer'],
            [['created', 'approved_time'], 'safe'],
            [['proposed_dispose', 'address_of_part_inspection', 'present_condition_of_boat'], 'string', 'max' => 500],
            [['approval_stage'], 'string', 'max' => 100],
            [['boat_number_id'], 'exist', 'skipOnError' => true, 'targetClass' => BoatNumbers::class, 'targetAttribute' => ['boat_number_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'boat_number_id' => Yii::t('app', 'Boat Number ID'),
            'repairable' => Yii::t('app', 'Repairable'),
            'parts_available_for_inspection' => Yii::t('app', 'Parts Available For Inspection'),
            'proposed_dispose' => Yii::t('app', 'Proposed Dispose'),
            'address_of_part_inspection' => Yii::t('app', 'Address Of Part Inspection'),
            'present_condition_of_boat' => Yii::t('app', 'Present Condition Of Boat'),
            'status' => Yii::t('app', 'Status'),
            'approval_stage' => Yii::t('app', 'Approval Stage'),
            'created' => Yii::t('app', 'Created'),
            'approved_time' => Yii::t('app', 'Approved Time'),
        ];
    }

    /**
     * Gets query for [[BoatNumber]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBoatNumber()
    {
        return $this->hasOne(BoatNumbers::class, ['id' => 'boat_number_id']);
    }
}
