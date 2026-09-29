<?php

namespace backend\models;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SkipperRenewSearch represents the model behind the search form of `backend\models\SkipperRenew`.
 */
class SkipperRenewSearch extends SkipperRenew
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fisherman_id', 'highest_education_qualification', 'fisheries_district', 'fisheries_division', 'status', 'printed'], 'integer'],
            [['skipper_uid', 'other_qualifications', 'approval_stage', 'created', 'approved_time', 'expire_date'], 'safe'],
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
        $query = SkipperRenew::find();

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
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'fisherman_id' => $this->fisherman_id,
            'highest_education_qualification' => $this->highest_education_qualification,
            'fisheries_district' => $this->fisheries_district,
            'fisheries_division' => $this->fisheries_division,
            'status' => $this->status,
            'created' => $this->created,
            'approved_time' => $this->approved_time,
            'expire_date' => $this->expire_date,
            'printed' => $this->printed,
        ]);

        $query->andFilterWhere(['like', 'skipper_uid', $this->skipper_uid])
            ->andFilterWhere(['like', 'other_qualifications', $this->other_qualifications])
            ->andFilterWhere(['like', 'approval_stage', $this->approval_stage]);

        return $dataProvider;
    }
}
