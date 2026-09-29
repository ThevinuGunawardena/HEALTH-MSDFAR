<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ProfileOfficers;

/**
 * ProfileOfficersSearch represents the model behind the search form of `backend\models\ProfileOfficers`.
 */
class ProfileOfficersSearch extends ProfileOfficers
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'district', 'division', 'status'], 'integer'],
            [['first_name', 'last_name', 'nic', 'current_designation', 'current_workplace_type', 'current_workplace', 'district_office', 'public_service_appointment_date', 'appointment_letter', 'dfar_appointment_date', 'dfar_appointment_letter', 'recruitment_method', 'w_op_number', 'appointment_status', 'date_of_birth', 'place_of_birth', 'permanent_address', 'harbour', 'signature', 'passport_copy', 'driving_license_copy', 'profile_image', 'agreement', 'user_level', 'device_serial', 'mobile_phone', 'home_phone', 'personal_email', 'photograph', 'it_result_sheet', 'cetificate'], 'safe'],
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
        $query = ProfileOfficers::find();

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
            'public_service_appointment_date' => $this->public_service_appointment_date,
            'dfar_appointment_date' => $this->dfar_appointment_date,
            'date_of_birth' => $this->date_of_birth,
            'district' => $this->district,
            'division' => $this->division,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'nic', $this->nic])
            ->andFilterWhere(['like', 'current_designation', $this->current_designation])
            ->andFilterWhere(['like', 'current_workplace_type', $this->current_workplace_type])
            ->andFilterWhere(['like', 'current_workplace', $this->current_workplace])
            ->andFilterWhere(['like', 'district_office', $this->district_office])
            ->andFilterWhere(['like', 'appointment_letter', $this->appointment_letter])
            ->andFilterWhere(['like', 'dfar_appointment_letter', $this->dfar_appointment_letter])
            ->andFilterWhere(['like', 'recruitment_method', $this->recruitment_method])
            ->andFilterWhere(['like', 'w_op_number', $this->w_op_number])
            ->andFilterWhere(['like', 'appointment_status', $this->appointment_status])
            ->andFilterWhere(['like', 'place_of_birth', $this->place_of_birth])
            ->andFilterWhere(['like', 'permanent_address', $this->permanent_address])
            ->andFilterWhere(['like', 'harbour', $this->harbour])
            ->andFilterWhere(['like', 'signature', $this->signature])
            ->andFilterWhere(['like', 'passport_copy', $this->passport_copy])
            ->andFilterWhere(['like', 'driving_license_copy', $this->driving_license_copy])
            ->andFilterWhere(['like', 'profile_image', $this->profile_image])
            ->andFilterWhere(['like', 'agreement', $this->agreement])
            ->andFilterWhere(['like', 'user_level', $this->user_level])
            ->andFilterWhere(['like', 'device_serial', $this->device_serial])
            ->andFilterWhere(['like', 'mobile_phone', $this->mobile_phone])
            ->andFilterWhere(['like', 'home_phone', $this->home_phone])
            ->andFilterWhere(['like', 'personal_email', $this->personal_email])
            ->andFilterWhere(['like', 'photograph', $this->photograph])
            ->andFilterWhere(['like', 'it_result_sheet', $this->it_result_sheet])
            ->andFilterWhere(['like', 'cetificate', $this->cetificate]);

        return $dataProvider;
    }
}
