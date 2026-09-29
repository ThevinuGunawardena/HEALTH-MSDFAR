<?php

namespace backend\models;

use backend\config\Constant;
use backend\config\UserTypeUtil;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ScientificDataSearch represents the model behind the search form of `backend\models\ScientificData`.
 */
class ScientificDataSearch extends ScientificData
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'district', 'division', 'landing_site', 'added_by', 'status'], 'integer'],
            [['start_time', 'end_time'], 'safe'],
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
        $query = ScientificData::find();

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
        if (UserTypeUtil::hasType(Constant::FI)) {
            $query->where(["district"=>Yii::$app->session->get("officer_district")]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'district' => $this->district,
            'division' => $this->division,
            'landing_site' => $this->landing_site,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'added_by' => $this->added_by,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }


    public function report1($from, $to, $district = "All")
    {
        $query = ScientificData::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->where(["status" => 1]);
        if ($district != "All") {
            $query->andWhere(["district" => $district]);
        }
        if ($from != null && $to != null) {
            $query->andWhere(['between', 'start_time', $from, $to]);
        }
        return $dataProvider;
    }
}
