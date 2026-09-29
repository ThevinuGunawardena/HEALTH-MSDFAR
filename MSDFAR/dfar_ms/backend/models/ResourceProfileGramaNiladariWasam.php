<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "resource_profile_grama_niladari_wasam".
 *
 * @property int $id
 * @property int $resource_profile_id
 * @property string $wasam_name
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ResourceProfile $resourceProfile
 */
class ResourceProfileGramaNiladariWasam extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'resource_profile_grama_niladari_wasam';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['resource_profile_id', 'wasam_name'], 'required'],
            [['resource_profile_id'], 'integer'],
            [['wasam_name'], 'string', 'max' => 255],
            [['created_at', 'updated_at'], 'safe'],
            [['resource_profile_id'], 'exist', 'skipOnError' => true, 'targetClass' => ResourceProfile::class, 'targetAttribute' => ['resource_profile_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'resource_profile_id' => 'Resource Profile ID',
            'wasam_name' => 'Grama Niladari Wasam Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[ResourceProfile]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResourceProfile()
    {
        return $this->hasOne(ResourceProfile::class, ['id' => 'resource_profile_id']);
    }
}
