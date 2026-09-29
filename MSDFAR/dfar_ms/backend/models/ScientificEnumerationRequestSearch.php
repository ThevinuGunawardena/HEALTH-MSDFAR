<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ScientificEnumerationRequestSearch represents the model behind the search form of `backend\models\ScientificEnumerationRequest`.
 */
class ScientificEnumerationRequestSearch extends ScientificEnumerationRequest
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user', 'status', 'district', 'division', 'landing_site'], 'integer'],
            [['request_date', 'date', 'can_continue', 'reson'], 'safe'],
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
        $query = ScientificEnumerationRequest::find();

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
            'user' => $this->user,
            'request_date' => $this->request_date,
            'date' => $this->date,
            'status' => $this->status,
            'district' => $this->district,
            'division' => $this->division,
            'landing_site' => $this->landing_site,
        ]);

        $query->andFilterWhere(['like', 'can_continue', $this->can_continue])
            ->andFilterWhere(['like', 'reson', $this->reson]);

        return $dataProvider;
    }
}
