<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\HardwareRepair;

/**
 * HardwareRepairSearch represents the model behind the search form of `app\models\HardwareRepair`.
 */
class HardwareRepairSearch extends HardwareRepair
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'Phone_number'], 'integer'],
            [['Name', 'Email', 'Office', 'Serial_number', 'Brand_name', 'Issue', 'Received_date', 'Status', 'Remarks'], 'safe'],
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
        $query = HardwareRepair::find();

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
            'Phone_number' => $this->Phone_number,
            'Received_date' => $this->Received_date,
        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'Email', $this->Email])
            ->andFilterWhere(['like', 'Office', $this->Office])
            ->andFilterWhere(['like', 'Serial_number', $this->Serial_number])
            ->andFilterWhere(['like', 'Brand_name', $this->Brand_name])
            ->andFilterWhere(['like', 'Issue', $this->Issue])
            ->andFilterWhere(['like', 'Status', $this->Status])
            ->andFilterWhere(['like', 'Remarks', $this->Remarks]);

        return $dataProvider;
    }
}
