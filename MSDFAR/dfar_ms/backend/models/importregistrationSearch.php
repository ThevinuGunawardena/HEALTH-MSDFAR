<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Importregistration;

/**
 * ImportregistrationSearch represents the model behind the search form of `backend\models\Importregistration`.
 */
class ImportregistrationSearch extends Importregistration
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['applicant_name', 'address', 'email', 'permit_type', 'commercial_name', 'imported_country', 'information', 'document'], 'safe'],
            [['mobile_number', 'fixed_number', 'id', 'business_reg_no'], 'integer'],
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
        $query = Importregistration::find();

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
            'mobile_number' => $this->mobile_number,
            'fixed_number' => $this->fixed_number,
            'id' => $this->id,
            'business_reg_no' => $this->business_reg_no,
            'total_weight' => $this->total_weight,
            'charge' => $this->charge,
        ]);

        $query->andFilterWhere(['like', 'applicant_name', $this->applicant_name])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'permit_type', $this->permit_type])
            ->andFilterWhere(['like', 'commercial_name', $this->commercial_name])
            ->andFilterWhere(['like', 'imported_country', $this->imported_country])
            ->andFilterWhere(['like', 'information', $this->information])
            ->andFilterWhere(['like', 'document', $this->document]);

        return $dataProvider;
    }
}
