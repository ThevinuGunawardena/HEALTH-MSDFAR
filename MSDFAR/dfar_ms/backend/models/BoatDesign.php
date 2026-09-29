<?php

namespace backend\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "boat_design".
 *
 * @property int $id
 * @property int $yard
 * @property int $boat_type
 * @property int $hull_material
 * @property int $engin_type
 * @property int $fi_district
 * @property string $design_notation
 * @property float $length
 * @property float $width
 * @property float $height
 * @property float $draft
 * @property string $remark
 * @property int $status
 *
 * @property MBoatTypes $boatType
 * @property BoatNumbers[] $boatNumbers
 * @property MFiDistrict $fiDistrict
 * @property ProfileYard $yard0
 */
class BoatDesign extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'boat_design';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['yard', 'boat_type', 'hull_material', 'engin_type', 'fi_district', 'design_notation', 'length', 'width', 'height', 'draft', 'remark', 'status'], 'required'],
            [['yard', 'boat_type', 'hull_material', 'engin_type', 'fi_district', 'status'], 'integer'],
            [['length', 'width', 'height', 'draft'], 'number'],
            [['design_notation'], 'string', 'max' => 200],
            [['remark'], 'string', 'max' => 500],
            [['boat_type'], 'exist', 'skipOnError' => true, 'targetClass' => MBoatTypes::class, 'targetAttribute' => ['boat_type' => 'id']],
            [['fi_district'], 'exist', 'skipOnError' => true, 'targetClass' => MFiDistrict::class, 'targetAttribute' => ['fi_district' => 'id']],
            [['yard'], 'exist', 'skipOnError' => true, 'targetClass' => ProfileYard::class, 'targetAttribute' => ['yard' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'yard' => Yii::t('app', 'Yard'),
            'boat_type' => Yii::t('app', 'Boat Type'),
            'hull_material' => Yii::t('app', 'Hull Material'),
            'engin_type' => Yii::t('app', 'Engin Type'),
            'fi_district' => Yii::t('app', 'Fi District'),
            'design_notation' => Yii::t('app', 'Design Notation'),
            'length' => Yii::t('app', 'Length'),
            'width' => Yii::t('app', 'Width'),
            'height' => Yii::t('app', 'Height'),
            'draft' => Yii::t('app', 'Draft'),
            'remark' => Yii::t('app', 'Remark'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Gets query for [[BoatType]].
     *
     * @return ActiveQuery
     */
    public function getBoatType()
    {
        return $this->hasOne(MBoatTypes::class, ['id' => 'boat_type']);
    }

    /**
     * Gets query for [[BoatNumbers]].
     *
     * @return ActiveQuery
     */
    public function getBoatNumbers()
    {
        return $this->hasMany(BoatNumbers::class, ['boat_design' => 'id']);
    }

    /**
     * Gets query for [[FiDistrict]].
     *
     * @return ActiveQuery
     */
    public function getFiDistrict()
    {
        return $this->hasOne(MFiDistrict::class, ['id' => 'fi_district']);
    }

    /**
     * Gets query for [[Yard0]].
     *
     * @return ActiveQuery
     */
    public function getYard0()
    {
        return $this->hasOne(ProfileYard::class, ['id' => 'yard']);
    }
}
