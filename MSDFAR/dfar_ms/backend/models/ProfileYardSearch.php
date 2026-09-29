<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ProfileYard;

/**
 * ProfileYardSearch represents the model behind the search form of `backend\models\ProfileYard`.
 */
class ProfileYardSearch extends ProfileYard
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'owner', 'admin_district', 'fisheries_district', 'division', 'transpotation_method'], 'integer'],
            [['name', 'address', 'mobile_number', 'land_line', 'email', 'web', 'fax', 'business_reg_no', 'business_reg_date', 'land_owner', 'deed_number', 'ownership_get_date', 'remark', 'gps_latitude', 'gps_longitude'], 'safe'],
            [['land_area', 'land_area_under_roof', 'distance_rural_hospital', 'distance_district_hospital', 'distance_base_hospital', 'distance_teaching_hospital', 'distance_genaral_hospital', 'distance_fire_brigade', 'distance_police_station'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ProfileYard::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'owner' => $this->owner,
            'business_reg_date' => $this->business_reg_date,
            'ownership_get_date' => $this->ownership_get_date,
            'land_area' => $this->land_area,
            'land_area_under_roof' => $this->land_area_under_roof,
            'admin_district' => $this->admin_district,
            'fisheries_district' => $this->fisheries_district,
            'division' => $this->division,
            'transpotation_method' => $this->transpotation_method,
            'distance_rural_hospital' => $this->distance_rural_hospital,
            'distance_district_hospital' => $this->distance_district_hospital,
            'distance_base_hospital' => $this->distance_base_hospital,
            'distance_teaching_hospital' => $this->distance_teaching_hospital,
            'distance_genaral_hospital' => $this->distance_genaral_hospital,
            'distance_fire_brigade' => $this->distance_fire_brigade,
            'distance_police_station' => $this->distance_police_station,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'mobile_number', $this->mobile_number])
            ->andFilterWhere(['like', 'land_line', $this->land_line])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'web', $this->web])
            ->andFilterWhere(['like', 'fax', $this->fax])
            ->andFilterWhere(['like', 'business_reg_no', $this->business_reg_no])
            ->andFilterWhere(['like', 'land_owner', $this->land_owner])
            ->andFilterWhere(['like', 'deed_number', $this->deed_number])
            ->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['like', 'gps_latitude', $this->gps_latitude])
            ->andFilterWhere(['like', 'gps_longitude', $this->gps_longitude]);

        return $dataProvider;
    }
}
