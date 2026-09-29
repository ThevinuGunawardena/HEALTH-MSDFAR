<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Application;

/**
 * ApplicationSearch represents the model behind the search form of `app\models\Application`.
 */
class ApplicationSearch extends Application
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['application_name', 'address', 'email', 'permit_type', 'commercial_name', 'area', 'country_export', 'information', 'document'], 'safe'],
            [['mobile', 'fax', 'reg_no', 'quantity_unit', 'total_number'], 'integer'],
            [['total_weight', 'charge'], 'number'],
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
        $query = Application::find();

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
            'mobile' => $this->mobile,
            'fax' => $this->fax,
            'reg_no' => $this->reg_no,
            'quantity_unit' => $this->quantity_unit,
            'total_weight' => $this->total_weight,
            'total_number' => $this->total_number,
            'charge' => $this->charge,
        ]);

        $query->andFilterWhere(['like', 'application_name', $this->application_name])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'permit_type', $this->permit_type])
            ->andFilterWhere(['like', 'commercial_name', $this->commercial_name])
            ->andFilterWhere(['like', 'area', $this->area])
            ->andFilterWhere(['like', 'country_export', $this->country_export])
            ->andFilterWhere(['like', 'information', $this->information])
            ->andFilterWhere(['like', 'document', $this->document]);

        return $dataProvider;
    }
}
