<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "export_company".
 *
 * @property int $id
 * @property string $company_name
 * @property string $address
 * @property int $district
 * @property string $br
 * @property string $appication_types
 * @property int $status
 * @property string $created
 * @property string $updated
 */
class ExportCompany extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'export_company';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_name', 'address', 'district', 'br', 'appication_types', 'status'], 'required'],
            [['district', 'status'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['company_name'], 'string', 'max' => 200],
            [['address'], 'string', 'max' => 500],
            [['br'], 'string', 'max' => 100],
//            [['appication_types'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'company_name' => Yii::t('app', 'Company Name'),
            'address' => Yii::t('app', 'Address'),
            'district' => Yii::t('app', 'District'),
            'br' => Yii::t('app', 'BR Number'),
            'appication_types' => Yii::t('app', 'Application Types'),
            'status' => Yii::t('app', 'Status'),
            'created' => Yii::t('app', 'Created'),
            'updated' => Yii::t('app', 'Updated'),
        ];
    }
}
