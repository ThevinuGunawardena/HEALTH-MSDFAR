<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DepartureSkipperSearch represents the model behind the search form of `backend\models\DepartureSkipper`.
 */
class DepartureSkipperSearch extends DepartureSkipper
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['skipper_name', 'crew_type', 'nic', 'skipper_id', 'address', 'contact', 'status', 'approved_by', 'timestamp', 'harbor', 'served_vessel', 'dep_date', 'dep_id', 'dep_cancel_allow_by', 'dep_cancel_date', 'to_date', 'remarks', 'offence_reason'], 'safe'],
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
        $query = DepartureSkipper::find();

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
            'timestamp' => $this->timestamp,
            'dep_date' => $this->dep_date,
            'dep_cancel_date' => $this->dep_cancel_date,
        ]);

        $query->andFilterWhere(['like', 'skipper_name', $this->skipper_name])
            ->andFilterWhere(['like', 'crew_type', $this->crew_type])
            ->andFilterWhere(['like', 'nic', $this->nic])
            ->andFilterWhere(['like', 'skipper_id', $this->skipper_id])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'contact', $this->contact])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'approved_by', $this->approved_by])
            ->andFilterWhere(['like', 'harbor', $this->harbor])
            ->andFilterWhere(['like', 'served_vessel', $this->served_vessel])
            ->andFilterWhere(['like', 'dep_id', $this->dep_id])
            ->andFilterWhere(['like', 'dep_cancel_allow_by', $this->dep_cancel_allow_by])
            ->andFilterWhere(['like', 'to_date', $this->to_date])
            ->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'offence_reason', $this->offence_reason]);

        return $dataProvider;
    }

    public function searchDepartureCancel($params)
    {
        $query = DepartureSkipper::find();

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
        $query->where(['!=', "status", "Departure Allowed"]);
        $query->andWhere(['!=', "status", ""]);
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'timestamp' => $this->timestamp,
            'dep_date' => $this->dep_date,
            'dep_cancel_date' => $this->dep_cancel_date,
        ]);

        $query->andFilterWhere(['like', 'skipper_name', $this->skipper_name])
            ->andFilterWhere(['like', 'crew_type', $this->crew_type])
            ->andFilterWhere(['like', 'nic', $this->nic])
            ->andFilterWhere(['like', 'skipper_id', $this->skipper_id])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'contact', $this->contact])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'approved_by', $this->approved_by])
            ->andFilterWhere(['like', 'harbor', $this->harbor])
            ->andFilterWhere(['like', 'served_vessel', $this->served_vessel])
            ->andFilterWhere(['like', 'dep_id', $this->dep_id])
            ->andFilterWhere(['like', 'dep_cancel_allow_by', $this->dep_cancel_allow_by])
            ->andFilterWhere(['like', 'to_date', $this->to_date])
            ->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'offence_reason', $this->offence_reason]);

        return $dataProvider;
    }
}
