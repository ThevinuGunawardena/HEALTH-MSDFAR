<?php

namespace backend\models;

use backend\config\Constant;
use backend\services\Util;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ApplicationtransportbechedemerSearch represents the model behind the search form of `backend\models\Applicationtransportbechedemer`.
 */
class ApplicationtransportbechedemerSearch extends Applicationtransportbechedemer
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name', 'permanent_address', 'mailing_address', 'email', 'nic_number', 'purchasing_district', 'store_district', 'final_destination', 'vehicle_number', 'boat_number', 'store_places', 'final_store_place', 'transport_number', 'vehicle_numbers', 'boat_numbers', 'store_place_details', 'document', 'supporting_document', 'vehicle', 'boat', 'species_type', 'weight_per_district', 'total_weight', 'intermediate_destination', 'quantity_pieces', 'approval_stage', 'created', 'approved_time', 'expire_date'], 'safe'],
            [['telephone', 'id', 'company', 'business_reg_number', 'status'], 'integer'],
            [['quantity'], 'number'],
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
        $query = Applicationtransportbechedemer::find();

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
            'telephone' => $this->telephone,
            'id' => $this->id,
            'company' => $this->company,
            'business_reg_number' => $this->business_reg_number,
            'quantity' => $this->quantity,
            'status' => $this->status,
            'created' => $this->created,
            'approved_time' => $this->approved_time,
            'expire_date' => $this->expire_date,
        ]);

        $query->andFilterWhere(['like', 'full_name', $this->full_name])
            ->andFilterWhere(['like', 'permanent_address', $this->permanent_address])
            ->andFilterWhere(['like', 'mailing_address', $this->mailing_address])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'nic_number', $this->nic_number])
            ->andFilterWhere(['like', 'purchasing_district', $this->purchasing_district])
            ->andFilterWhere(['like', 'store_district', $this->store_district])
            ->andFilterWhere(['like', 'final_destination', $this->final_destination])
            ->andFilterWhere(['like', 'vehicle_number', $this->vehicle_number])
            ->andFilterWhere(['like', 'boat_number', $this->boat_number])
            ->andFilterWhere(['like', 'store_places', $this->store_places])
            ->andFilterWhere(['like', 'final_store_place', $this->final_store_place])
            ->andFilterWhere(['like', 'transport_number', $this->transport_number])
            ->andFilterWhere(['like', 'vehicle_numbers', $this->vehicle_numbers])
            ->andFilterWhere(['like', 'boat_numbers', $this->boat_numbers])
            ->andFilterWhere(['like', 'store_place_details', $this->store_place_details])
            ->andFilterWhere(['like', 'document', $this->document])
            ->andFilterWhere(['like', 'supporting_document', $this->supporting_document])
            ->andFilterWhere(['like', 'vehicle', $this->vehicle])
            ->andFilterWhere(['like', 'boat', $this->boat])
            ->andFilterWhere(['like', 'species_type', $this->species_type])
            ->andFilterWhere(['like', 'weight_per_district', $this->weight_per_district])
            ->andFilterWhere(['like', 'total_weight', $this->total_weight])
            ->andFilterWhere(['like', 'intermediate_destination', $this->intermediate_destination])
            ->andFilterWhere(['like', 'quantity_pieces', $this->quantity_pieces])
            ->andFilterWhere(['like', 'approval_stage', $this->approval_stage]);

        return $dataProvider;
    }
}
