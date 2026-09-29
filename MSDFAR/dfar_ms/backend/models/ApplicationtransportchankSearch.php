<?php

namespace backend\models;

use backend\config\Constant;
use backend\services\Util;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ApplicationtransportchankSearch represents the model behind the search form of `backend\models\Applicationtransportchank`.
 */
class ApplicationtransportchankSearch extends Applicationtransportchank
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name', 'permanent_address', 'telephone_no', 'email', 'national_id', 'business_registration_no', 'purchasing_district', 'processing_district', 'final_storing_district', 'store_place', 'final_store_place', 'supporting_document', 'quantity_type', 'contact_value', 'quantity_value', 'store_place_details', 'species_type', 'weight_per_district', 'total_weight', 'intermediate_destination', 'final_destination', 'vehicle_numbers', 'boat_numbers', 'vehicle', 'boat', 'nic_number', 'business_reg_number', 'request_date', 'approval_stage', 'created', 'approved_time', 'expire_date'], 'safe'],
            [['id', 'company', 'quantity_kg', 'quantity_pieces', 'status'], 'integer'],
            [['tnc'], 'string'],
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
        $query = Applicationtransportchank::find();

        // add conditions that should always apply here
        if (Yii::$app->user->identity->type == Constant::EXPORT_COMPANY && Util::editPermission()) {
            $query->where(["company" => Yii::$app->user->identity->profile_id]);
        }
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
            'company' => $this->company,
            'quantity_kg' => $this->quantity_kg,
            'quantity_pieces' => $this->quantity_pieces,
            'request_date' => $this->request_date,
            'status' => $this->status,
            'created' => $this->created,
            'approved_time' => $this->approved_time,
            'expire_date' => $this->expire_date,
        ]);

        $query->andFilterWhere(['like', 'full_name', $this->full_name])
            ->andFilterWhere(['like', 'permanent_address', $this->permanent_address])
            ->andFilterWhere(['like', 'telephone_no', $this->telephone_no])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'national_id', $this->national_id])
            ->andFilterWhere(['like', 'business_registration_no', $this->business_registration_no])
            ->andFilterWhere(['like', 'purchasing_district', $this->purchasing_district])
            ->andFilterWhere(['like', 'processing_district', $this->processing_district])
            ->andFilterWhere(['like', 'final_storing_district', $this->final_storing_district])
            ->andFilterWhere(['like', 'store_place', $this->store_place])
            ->andFilterWhere(['like', 'final_store_place', $this->final_store_place])
            ->andFilterWhere(['like', 'supporting_document', $this->supporting_document])
            ->andFilterWhere(['like', 'quantity_type', $this->quantity_type])
            ->andFilterWhere(['like', 'contact_value', $this->contact_value])
            ->andFilterWhere(['like', 'quantity_value', $this->quantity_value])
            ->andFilterWhere(['like', 'store_place_details', $this->store_place_details])
            ->andFilterWhere(['like', 'species_type', $this->species_type])
            ->andFilterWhere(['like', 'weight_per_district', $this->weight_per_district])
            ->andFilterWhere(['like', 'total_weight', $this->total_weight])
            ->andFilterWhere(['like', 'intermediate_destination', $this->intermediate_destination])
            ->andFilterWhere(['like', 'final_destination', $this->final_destination])
            ->andFilterWhere(['like', 'vehicle_numbers', $this->vehicle_numbers])
            ->andFilterWhere(['like', 'boat_numbers', $this->boat_numbers])
            ->andFilterWhere(['like', 'vehicle', $this->vehicle])
            ->andFilterWhere(['like', 'boat', $this->boat])
            ->andFilterWhere(['like', 'nic_number', $this->nic_number])
            ->andFilterWhere(['like', 'business_reg_number', $this->business_reg_number])
            ->andFilterWhere(['like', 'approval_stage', $this->approval_stage]);

        return $dataProvider;
    }
}
