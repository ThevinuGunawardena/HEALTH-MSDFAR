<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Kpi;

/**
 * KpiSearch represents the model behind the search form of `backend\models\Kpi`.
 */
class KpiSearch extends Kpi
{
    // Add these virtual attributes to handle string filters
    public $divisionName;
    public $supervisorName;
    public $responsibilityName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['KPIId', 'divisionId', 'supervisor', 'responsibility'], 'integer'],
            [['title', 'indicator', 'unit', 'rational', 'createdDate', 'assignedDate', 'targetType', 'targetDate', 'progress', 'status'], 'safe'],
            [['target'], 'number'],
            [['divisionName', 'supervisorName', 'responsibilityName'], 'safe'],
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
    $query = Kpi::find();

    // 💡 Join with the kpi_divisions relation table automatically
    $query->joinWith(['division']); 

    $dataProvider = new \yii\data\ActiveDataProvider([
        'query' => $query,
    ]);

    // 💡 Setup sorting rules so clicking the "Division" column header still works
    $dataProvider->sort->attributes['divisionName'] = [
        'asc' => ['kpi_divisions.divisionName' => SORT_ASC],
        'desc' => ['kpi_divisions.divisionName' => SORT_DESC],
    ];

    if (!($this->load($params) && $this->validate())) {
        return $dataProvider;
    }

    // Grid strict attribute filters
    $query->andFilterWhere([
        'kpi.KPIId' => $this->KPIId,
        'kpi.target' => $this->target,
        'kpi.progress' => $this->progress,
        'kpi.createdDate' => $this->createdDate,
        'kpi.assignedDate' => $this->assignedDate,
        'kpi.targetDate' => $this->targetDate,
        'kpi.supervisor' => $this->supervisor,
        'kpi.responsibility' => $this->responsibility,
    ]);

    // String attributes fuzzy filters
    $query->andFilterWhere(['like', 'kpi.title', $this->title])
          ->andFilterWhere(['like', 'kpi.indicator', $this->indicator])
          ->andFilterWhere(['like', 'kpi.unit', $this->unit])
          ->andFilterWhere(['like', 'kpi.status', $this->status]);

    // 💡 Apply the custom relation filter rule for the division name match
    $query->andFilterWhere(['like', 'kpi_divisions.divisionName', $this->divisionName]);

    return $dataProvider;
}
}