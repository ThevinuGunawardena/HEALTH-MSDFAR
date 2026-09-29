<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\CatchDataFishcatch;

/**
 * CatchDataFishcatchSearch represents the model behind the search form of `backend\models\CatchDataFishcatch`.
 */
class CatchDataFishcatchSearch extends CatchDataFishcatch
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fish_type', 'num_of_fish_log', 'weight_of_fish_log', 'num_of_fish_act', 'weight_of_fish_act', 'catchdata_req_id'], 'integer'],
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
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = CatchDataFishcatch::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'fish_type' => $this->fish_type,
            'num_of_fish_log' => $this->num_of_fish_log,
            'weight_of_fish_log' => $this->weight_of_fish_log,
            'num_of_fish_act' => $this->num_of_fish_act,
            'weight_of_fish_act' => $this->weight_of_fish_act,
            'catchdata_req_id' => $this->catchdata_req_id,
        ]);

        return $dataProvider;
    }
}
