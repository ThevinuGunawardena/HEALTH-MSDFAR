<?php

namespace backend\models;

use backend\config\Constant;
use backend\services\Util;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ApplicationtransportnaklaSearch represents the model behind the search form of `backend\models\Applicationtransportnakla`.
 */
class ApplicationtransportnaklaSearch extends Applicationtransportnakla
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name', 'permanent_address', 'nic_number', 'destination_place', 'vehicle_number', 'boat_number', 'store_places', 'contact_value', 'quantity_value', 'transport_value', 'vehicle_numbers', 'boat_numbers', 'store_place_details', 'contact_type', 'document', 'supporting_document', 'species_type', 'weight_per_district', 'total_weight', 'intermediate_destination', 'final_destination', 'vehicle', 'boat', 'purchasing_district', 'request_date', 'approval_stage', 'created', 'approved_time', 'expire_date'], 'safe'],
            [['fax_number', 'id', 'company', 'business_reg_number', 'telephone', 'telephone_number', 'status'], 'integer'],
            [['quantity_kg', 'quantity_pieces'], 'number'],
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
        $query = Applicationtransportnakla::find();

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
            'fax_number' => $this->fax_number,
            'id' => $this->id,
            'company' => $this->company,
            'business_reg_number' => $this->business_reg_number,
            'quantity_kg' => $this->quantity_kg,
            'quantity_pieces' => $this->quantity_pieces,
            'telephone' => $this->telephone,
            'telephone_number' => $this->telephone_number,
            'request_date' => $this->request_date,
            'status' => $this->status,
            'created' => $this->created,
            'approved_time' => $this->approved_time,
            'expire_date' => $this->expire_date,
        ]);

        $query->andFilterWhere(['like', 'full_name', $this->full_name])
            ->andFilterWhere(['like', 'permanent_address', $this->permanent_address])
            ->andFilterWhere(['like', 'nic_number', $this->nic_number])
            ->andFilterWhere(['like', 'destination_place', $this->destination_place])
            ->andFilterWhere(['like', 'vehicle_number', $this->vehicle_number])
            ->andFilterWhere(['like', 'boat_number', $this->boat_number])
            ->andFilterWhere(['like', 'store_places', $this->store_places])
            ->andFilterWhere(['like', 'contact_value', $this->contact_value])
            ->andFilterWhere(['like', 'quantity_value', $this->quantity_value])
            ->andFilterWhere(['like', 'transport_value', $this->transport_value])
            ->andFilterWhere(['like', 'vehicle_numbers', $this->vehicle_numbers])
            ->andFilterWhere(['like', 'boat_numbers', $this->boat_numbers])
            ->andFilterWhere(['like', 'store_place_details', $this->store_place_details])
            ->andFilterWhere(['like', 'contact_type', $this->contact_type])
            ->andFilterWhere(['like', 'document', $this->document])
            ->andFilterWhere(['like', 'supporting_document', $this->supporting_document])
            ->andFilterWhere(['like', 'species_type', $this->species_type])
            ->andFilterWhere(['like', 'weight_per_district', $this->weight_per_district])
            ->andFilterWhere(['like', 'total_weight', $this->total_weight])
            ->andFilterWhere(['like', 'intermediate_destination', $this->intermediate_destination])
            ->andFilterWhere(['like', 'final_destination', $this->final_destination])
            ->andFilterWhere(['like', 'vehicle', $this->vehicle])
            ->andFilterWhere(['like', 'boat', $this->boat])
            ->andFilterWhere(['like', 'purchasing_district', $this->purchasing_district])
            ->andFilterWhere(['like', 'approval_stage', $this->approval_stage]);

        return $dataProvider;
    }
}
