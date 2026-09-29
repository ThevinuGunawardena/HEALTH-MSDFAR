<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Transportregistration;

/**
 * TransportregistrationSearch represents the model behind the search form of `backend\models\Transportregistration`.
 */
class TransportregistrationSearch extends Transportregistration
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['applicant_name', 'address', 'email', 'nid_number', 'mail_address', 'permit_type', 'type', 'area', 'vehicle_number', 'destination_district', 'product_storing_area', 'finalstoreplace_address', 'import_country', 'document'], 'safe'],
            [['mobile_number', 'reg_number', 'id', 'quantity'], 'integer'],
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
        $query = Transportregistration::find();

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
            'reg_number' => $this->reg_number,
            'id' => $this->id,
            'quantity' => $this->quantity,
        ]);

        $query->andFilterWhere(['like', 'applicant_name', $this->applicant_name])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'nid_number', $this->nid_number])
            ->andFilterWhere(['like', 'mail_address', $this->mail_address])
            ->andFilterWhere(['like', 'permit_type', $this->permit_type])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'area', $this->area])
            ->andFilterWhere(['like', 'vehicle_number', $this->vehicle_number])
            ->andFilterWhere(['like', 'destination_district', $this->destination_district])
            ->andFilterWhere(['like', 'product_storing_area', $this->product_storing_area])
            ->andFilterWhere(['like', 'finalstoreplace_address', $this->finalstoreplace_address])
            ->andFilterWhere(['like', 'import_country', $this->import_country])
            ->andFilterWhere(['like', 'document', $this->document]);

        return $dataProvider;
    }
}
