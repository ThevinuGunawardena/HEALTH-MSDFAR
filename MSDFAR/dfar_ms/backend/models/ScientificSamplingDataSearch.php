<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ScientificSamplingDataSearch represents the model behind the search form of `backend\models\ScientificSamplingData`.
 */
class ScientificSamplingDataSearch extends ScientificSamplingData
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'scientific_id', 'status'], 'integer'],
            [['boat_number'], 'safe'],
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
        $query = ScientificSamplingData::find();

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
            'scientific_id' => $this->scientific_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'boat_number', $this->boat_number]);

        return $dataProvider;
    }

    public function report1($from, $to, $district = "All")
    {
        $query = ScientificSamplingData::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
//        $query->where(["status" => 1]);
//        if ($district != "All") {
//            $query->andWhere(["district" => $district]);
//        }
//        if ($from != null && $to != null) {
//            $query->andWhere(['between', 'start_time', $from, $to]);
//        }
        return $dataProvider;
    }
}
