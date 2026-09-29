<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\FishermanDependant;

/**
 * FishermanDependantSearch represents the model behind the search form of `backend\models\FishermanDependant`.
 */
class FishermanDependantSearch extends FishermanDependant
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fisherman_id', 'type'], 'integer'],
            [['name', 'nic', 'birthday'], 'safe'],
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
        $query = FishermanDependant::find();

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
            'fisherman_id' => $this->fisherman_id,
            'type' => $this->type,
            'birthday' => $this->birthday,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'nic', $this->nic]);

        return $dataProvider;
    }
}
