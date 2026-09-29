<?php
namespace backend\models;

use Yii;

class ResourceProfile extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'resource_profile';
    }

    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'fishing_population', 'fisheries_families', 'active_fishermen',
            'fishing_households', 'fishing_households_without_sanitary',
            'fishing_households_without_drinking_water', 'status', 'm_division_id'], 'integer'],
            [['user_id'], 'exist',
            'skipOnError' => true,
            'targetClass' => User::class,
            'targetAttribute' => ['user_id' => 'id']
            ],
            [['m_division_id'], 'exist',
            'skipOnError' => true,
            'targetClass' => MDivision::class,
            'targetAttribute' => ['m_division_id' => 'id']
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'm_division_id' => 'FI Division Area',
            'fishing_population' => 'Fishing Population',
            'fisheries_families' => 'Fisheries Families',
            'active_fishermen' => 'Active Fishermen',
            'fishing_households' => 'Fishing Households',
            'fishing_households_without_sanitary' => 'Fishing Households Without Sanitary',
            'fishing_households_without_drinking_water' => 'Fishing Households Without Drinking Water',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getDivision()
    {
        return $this->hasOne(MDivision::class, ['id' => 'm_division_id']);
    }

    public function getGramaNiladariWasams()
    {
        return $this->hasMany(ResourceProfileGramaNiladariWasam::class, ['resource_profile_id' => 'id']);
    }

    public function getFishingVillages()
    {
        return $this->hasMany(ResourceProfileFishingVillages::class, ['resource_profile_id' => 'id']);
    }

    public function getLandingSites()
    {
        return $this->hasMany(ResourceProfileLandingSites::class, ['resource_profile_id' => 'id']);
    }

    public function getBeachSites()
    {
        return $this->hasMany(ResourceProfileBeachSites::class, ['resource_profile_id' => 'id']);
    }

    public function getIceFactories()
    {
        return $this->hasMany(ResourceProfileIceFactories::class, ['resource_profile_id' => 'id']);
    }

    public function getExporters()
    {
        return $this->hasMany(ResourceProfileExporters::class, ['resource_profile_id' => 'id']);
    }

    public function getBoatBuildingYards()
    {
        return $this->hasMany(ResourceProfileBoatBuildingYards::class, ['resource_profile_id' => 'id']);
    }

    public function getDryFishManufactures()
    {
        return $this->hasMany(ResourceProfileDryFishManufactures::class, ['resource_profile_id' => 'id']);
    }

    public function getFisheriesCooperateSocieties()
    {
        return $this->hasMany(ResourceProfileFisheriesCooperateSociety::class, ['resource_profile_id' => 'id']);
    }

    public function getFisheriesRuralSocieties()
    {
        return $this->hasMany(ResourceProfileFisheriesRuralSociety::class, ['resource_profile_id' => 'id']);
    }

    public function getGovernmentOffices()
    {
        return $this->hasMany(ResourceProfileGovernmentOffices::class, ['resource_profile_id' => 'id']);
    }

    public function getFisheriesRoads()
    {
        return $this->hasMany(ResourceProfileFisheriesRoads::class, ['resource_profile_id' => 'id']);
    }

    public function getOthers()
    {
        return $this->hasMany(ResourceProfileOthers::class, ['resource_profile_id' => 'id']);
    }

    public function getPoliceStations()
    {
        return $this->hasMany(ResourceProfilePoliceStations::class, ['resource_profile_id' => 'id']);
    }

    public function getSpecialProjects()
    {
        return $this->hasMany(ResourceProfileSpecialProjects::class, ['resource_profile_id' => 'id']);
    }

    public function getTraditionalFishings()
    {
        return $this->hasMany(ResourceProfileTraditionalFishing::class, ['resource_profile_id' => 'id']);
    }
}