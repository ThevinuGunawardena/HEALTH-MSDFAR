<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Inquiry;

/**
 * InquirySearch represents the model behind the search form of `backend\models\Inquiry`.
 */
class InquirySearch extends Inquiry
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Inquiry_ID', 'id'], 'integer'],
            [['Name', 'phone_number', 'Email', 'Office', 'Inquiry_Type', 'Subject', 'Description', 'Submission_Date', 'Inquiry_Status', 'remarks', 'Response', 'completion_date', 'status'], 'safe'],
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
        $query = Inquiry::find();

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
            'Inquiry_ID' => $this->Inquiry_ID,
            'id' => $this->id,
            'Submission_Date' => $this->Submission_Date,
            'completion_date' => $this->completion_date,


        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['Inquiry_Type' => $this->Inquiry_Type])
            ->andFilterWhere(['Inquiry_Status' => $this->Inquiry_Status])
            ->andFilterWhere(['Office' => $this->Office]);

        return $dataProvider;
    }
}
