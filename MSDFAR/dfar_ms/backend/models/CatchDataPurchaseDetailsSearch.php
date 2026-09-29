<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\CatchDataPurchaseDetails;

/**
 * CatchDataPurchaseDetailsSearch represents the model behind the search form of `backend\models\CatchDataPurchaseDetails`.
 */
class CatchDataPurchaseDetailsSearch extends CatchDataPurchaseDetails
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fish_type', 'No_of_Fish', 'Weight_of_Fish', 'exporter_uid', 'catch_data_request_id'], 'integer'],
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
        $query = CatchDataPurchaseDetails::find();

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
            'No_of_Fish' => $this->No_of_Fish,
            'Weight_of_Fish' => $this->Weight_of_Fish,
            'exporter_uid' => $this->exporter_uid,
            'catch_data_request_id' => $this->catch_data_request_id,
        ]);

        return $dataProvider;
    }
}
