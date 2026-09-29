<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\FisheriesProgress;

/**
 * FisheriesProgressSearch represents the model behind the search form of `backend\models\FisheriesProgress`.
 */
class FisheriesProgressSearch extends FisheriesProgress
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'district', 'officer', 'timestamp'], 'integer'],
            [['month'], 'safe'],
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
    $query = FisheriesProgress::find();

    $dataProvider = new ActiveDataProvider([
        'query' => $query,
    ]);

    $this->load($params, $formName);

    if (!$this->validate()) {
        return $dataProvider;
    }

    // Get current month and previous month
    $currentMonth = date('Y-m-01'); // Current month in 'YYYY-MM' format
    $previousMonth = date('Y-m-01', strtotime("first day of last month")); // Previous month

    // Apply filtering based on current and previous month
    $query->andFilterWhere(['month' => [$currentMonth, $previousMonth]]);

    // Grid filtering conditions
    $query->andFilterWhere([
        'id' => $this->id,
        'district' => $this->district,
        'officer' => $this->officer,
        'timestamp' => $this->timestamp,
    ]);

    return $dataProvider;
}


}
