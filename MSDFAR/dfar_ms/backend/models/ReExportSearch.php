<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Reexport;

/**
 * ReexportSearch represents the model behind the search form of `backend\models\Reexport`.
 */
class ReexportSearch extends Reexport
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['applicant_name', 'permanent_address', 'email', 'permit_type', 'commercial_name', 'export_country', 'document'], 'safe'],
            [['id', 'business_reg_number'], 'integer'],
            [['total_weight'], 'number'],
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
        $query = Reexport::find();

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
            'business_reg_number' => $this->business_reg_number,
            'total_weight' => $this->total_weight,
        ]);

        $query->andFilterWhere(['like', 'applicant_name', $this->applicant_name])
            ->andFilterWhere(['like', 'permanent_address', $this->permanent_address])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'permit_type', $this->permit_type])
            ->andFilterWhere(['like', 'commercial_name', $this->commercial_name])
            ->andFilterWhere(['like', 'export_country', $this->export_country])
            ->andFilterWhere(['like', 'document', $this->document]);

        return $dataProvider;
    }
}
