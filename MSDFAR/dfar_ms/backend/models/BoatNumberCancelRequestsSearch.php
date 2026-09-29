<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\BoatNumberCancelRequests;

/**
 * BoatNumberCancelRequestsSearch represents the model behind the search form of `backend\models\BoatNumberCancelRequests`.
 */
class BoatNumberCancelRequestsSearch extends BoatNumberCancelRequests
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'boat_number_id', 'repairable', 'parts_available_for_inspection', 'status'], 'integer'],
            [['proposed_dispose', 'address_of_part_inspection', 'present_condition_of_boat', 'approval_stage'], 'safe'],
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
        $query = BoatNumberCancelRequests::find();

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
            'boat_number_id' => $this->boat_number_id,
            'repairable' => $this->repairable,
            'parts_available_for_inspection' => $this->parts_available_for_inspection,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'proposed_dispose', $this->proposed_dispose])
            ->andFilterWhere(['like', 'address_of_part_inspection', $this->address_of_part_inspection])
            ->andFilterWhere(['like', 'present_condition_of_boat', $this->present_condition_of_boat])
            ->andFilterWhere(['like', 'approval_stage', $this->approval_stage]);

        return $dataProvider;
    }
}
