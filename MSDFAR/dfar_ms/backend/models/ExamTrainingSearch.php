<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ExamTraining;

/**
 * ExamTrainingSearch represents the model behind the search form of `backend\models\ExamTraining`.
 */
class ExamTrainingSearch extends ExamTraining
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'profile_officer_id'], 'integer'],
            [['exam_training_name', 'exam_training_year', 'exam_training_institute', 'results_certificate'], 'safe'],
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
        $query = ExamTraining::find();

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
            'profile_officer_id' => $this->profile_officer_id,
            'exam_training_year' => $this->exam_training_year,
        ]);

        $query->andFilterWhere(['like', 'exam_training_name', $this->exam_training_name])
            ->andFilterWhere(['like', 'exam_training_institute', $this->exam_training_institute])
            ->andFilterWhere(['like', 'results_certificate', $this->results_certificate]);

        return $dataProvider;
    }
}
