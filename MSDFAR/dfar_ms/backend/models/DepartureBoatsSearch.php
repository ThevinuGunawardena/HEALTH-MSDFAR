<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DepartureBoatsSearch represents the model behind the search form of `backend\models\DepartureBoats`.
 */
class DepartureBoatsSearch extends DepartureBoats
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'boat_number_id', 'fisherman_id'], 'integer'],
            [['status', 'timestamp', 'harbor', 'district', 'date_violation', 'dep_cancelled_by', 'dep_cancel_date', 'remarks', 'offence', 'to_date'], 'safe'],
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
        $query = DepartureBoats::find()
        ->alias('dboat')
        ->joinWith(['boat boat'])
        ->with(['latestBluetrakerReport'])
        ->where(['boat.status' => 101]);

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
            'dboat.boat_number_id' => $this->boat_number_id,
            'dboat.fisherman_id' => $this->fisherman_id,
            'dboat.timestamp' => $this->timestamp,
            'dboat.dep_cancel_date' => $this->dep_cancel_date,
        ]);

        $query->andFilterWhere(['like', 'dboat.status', $this->status])
            ->andFilterWhere(['like', 'dboat.harbor', $this->harbor])
            ->andFilterWhere(['like', 'dboat.district', $this->district])
            ->andFilterWhere(['like', 'dboat.date_violation', $this->date_violation])
            ->andFilterWhere(['like', 'dboat.dep_cancelled_by', $this->dep_cancelled_by])
            ->andFilterWhere(['like', 'dboat.remarks', $this->remarks])
            ->andFilterWhere(['like', 'dboat.offence', $this->offence])
            ->andFilterWhere(['like', 'dboat.to_date', $this->to_date]);

        return $dataProvider;
    }

    public function searchDepartureCancel($params)
    {
        $query = DepartureBoats::find();

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
            'boat_number_id' => $this->boat_number_id,
            'fisherman_id' => $this->fisherman_id,
            'timestamp' => $this->timestamp,
            'dep_cancel_date' => $this->dep_cancel_date,
        ]);

        $query
            ->andFilterWhere(['like', 'harbor', $this->harbor])
            ->andFilterWhere(['like', 'district', $this->district])
            ->andFilterWhere(['like', 'date_violation', $this->date_violation])
            ->andFilterWhere(['like', 'dep_cancelled_by', $this->dep_cancelled_by])
            ->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'offence', $this->offence])
            ->andFilterWhere(['like', 'to_date', $this->to_date]);

        return $dataProvider;
    }
}
