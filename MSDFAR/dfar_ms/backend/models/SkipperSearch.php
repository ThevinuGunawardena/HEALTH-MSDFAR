<?php

namespace backend\models;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SkipperSearch represents the model behind the search form of `backend\models\Skipper`.
 */
class SkipperSearch extends Skipper
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fisherman_id', 'highest_education_qualification', 'fisheries_district', 'fisheries_division', 'status'], 'integer'],
            [['other_qualifications'], 'safe'],
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
        $query = Skipper::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->sort->defaultOrder = ['id' => SORT_DESC];
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if ($this->status == Constant::Pending) {
            if (UserTypeUtil::hasType(Constant::FI)) {
                $query->where(["fisheries_division" => Yii::$app->session->get("officer_division"), 'approval_stage' => Constant::FI]);
            }
            if (UserTypeUtil::hasType(Constant::DO)) {
                $query->where(["fisheries_district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::DO]);
            }
            if (UserTypeUtil::hasType(Constant::AD)) {
                $query->where(["fisheries_district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::AD]);
            }
            if (UserTypeUtil::hasType(Constant::DM)) {
                $query->where(['approval_stage' => Constant::DM]);
            }
            if (UserTypeUtil::hasType(Constant::DG)) {
                $query->where(['approval_stage' => Constant::DG]);
            }
        } else {
            if (UserTypeUtil::hasType(Constant::FI)) {
                $query->where(["fisheries_division" => Yii::$app->session->get("officer_division")]);
            }
            if (UserTypeUtil::hasType(Constant::DO)) {
                $query->where(["fisheries_district" => Yii::$app->session->get("officer_district")]);
            }
            if (UserTypeUtil::hasType(Constant::AD)) {
                $query->where(["fisheries_district" => Yii::$app->session->get("officer_district")]);
            }
        }
        if ($this->status == Constant::InProgress) {
            $query->andWhere(['!=', 'status', Constant::Active]);
            $query->andWhere(['!=', 'status', Constant::Inactive]);
            $query->andWhere(['!=', 'status', Constant::Expired]);
            $query->andWhere(['!=', 'status', Constant::Transferred]);
            $query->andWhere(['!=', 'status', Constant::Cancelled]);
        } else if ($this->status != "") {
            $query->andWhere(['status' => $this->status]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'fisherman_id' => $this->fisherman_id,
            'highest_education_qualification' => $this->highest_education_qualification,
            'fisheries_district' => $this->fisheries_district,
            'fisheries_division' => $this->fisheries_division,
        ]);

        $query->andFilterWhere(['like', 'other_qualifications', $this->other_qualifications]);

        return $dataProvider;
    }

    public function skipperReport($from, $to, $district = "All")
    {
        $query = Skipper::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->where(["status" => Constant::Active]);
        if ($district != "All") {
            $query->andWhere(["fisheries_district" => $district]);
        }
        if ($from != null && $to != null) {
            $query->andWhere(['between', 'created', $from, $to]);
        }
        return $dataProvider;
    }
}
