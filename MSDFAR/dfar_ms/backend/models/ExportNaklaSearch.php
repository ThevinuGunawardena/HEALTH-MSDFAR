<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Exportnakla;

/**
 * ExportnaklaSearch represents the model behind the search form of `backend\models\Exportnakla`.
 */
class ExportnaklaSearch extends Exportnakla
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name', 'permanent_address', 'nic_number', 'purchase_place', 'export_countries'], 'safe'],
            [['telephone_number', 'fax_number', 'business_reg_number', 'previouspermit_exported_quantity', 'export_quantity'], 'integer'],
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
        $query = Exportnakla::find();

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
            'telephone_number' => $this->telephone_number,
            'fax_number' => $this->fax_number,
            'business_reg_number' => $this->business_reg_number,
            'previouspermit_exported_quantity' => $this->previouspermit_exported_quantity,
            'export_quantity' => $this->export_quantity,
        ]);

        $query->andFilterWhere(['like', 'full_name', $this->full_name])
            ->andFilterWhere(['like', 'permanent_address', $this->permanent_address])
            ->andFilterWhere(['like', 'nic_number', $this->nic_number])
            ->andFilterWhere(['like', 'purchase_place', $this->purchase_place])
            ->andFilterWhere(['like', 'export_countries', $this->export_countries]);

        return $dataProvider;
    }
}
