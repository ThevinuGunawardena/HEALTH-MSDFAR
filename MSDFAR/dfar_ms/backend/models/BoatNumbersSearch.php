<?php

namespace backend\models;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * BoatNumbersSearch represents the model behind the search form of `backend\models\BoatNumbers`.
 */
class BoatNumbersSearch extends BoatNumbers
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'yard', 'boat_design', 'owner', 'boat_type', 'fisheries_district', 'fisheries_division', 'status'], 'integer'],
            [['boat_number', 'remarks', 'approval_stage', 'created', 'approved_time', 'expire_date'], 'safe'],
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
        $query = BoatNumbers::find()->alias('bn')->select('bn.*')
            ->joinWith(['yard0 y']);

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
                $query->where(["y.fisheries_division" => Yii::$app->session->get("officer_division"), 'bn.approval_stage' => Constant::FI]);
            }
            if (UserTypeUtil::hasType(Constant::AD)) {
                $query->where(["y.fisheries_district" => Yii::$app->session->get("officer_district"), 'bn.approval_stage' => Constant::AD]);
            }

            if (UserTypeUtil::hasType(Constant::DFI)) {
                $query->where(["y.fisheries_district" => Yii::$app->session->get("officer_district"), 'bn.approval_stage' => Constant::DFI]);
            }
            if (UserTypeUtil::hasType(Constant::DO)) {
                $query->where(["y.fisheries_district" => Yii::$app->session->get("officer_district"), 'bn.approval_stage' => Constant::DO]);
            }
            if (UserTypeUtil::hasType(Constant::DM)) {
                $query->where(['bn.approval_stage' => Constant::DM]);
            }
            if (UserTypeUtil::hasType(Constant::DG)) {
                $query->where(['bn.approval_stage' => Constant::DG]);
            }
        } else {
            if (UserTypeUtil::hasType(Constant::FI)) {
                $query->where(["y.division" => Yii::$app->session->get("officer_division")]);
            }
            if (UserTypeUtil::hasType(Constant::AD)) {
                $query->where(["y.fisheries_district" => Yii::$app->session->get("officer_district")]);
            }

            if (UserTypeUtil::hasType(Constant::DFI)) {
                $query->where(["y.fisheries_district" => Yii::$app->session->get("officer_district")]);
            }
            if (UserTypeUtil::hasType(Constant::DO)) {
                $query->where(["y.fisheries_district" => Yii::$app->session->get("officer_district")]);
            }
        }

        if ($this->status != "") {
            if ($this->status == Constant::InProgress) {

                $query->andWhere(['!=', 'bn.status', Constant::Active]);
                $query->andWhere(['!=', 'bn.status', Constant::Inactive]);
                $query->andWhere(['!=', 'bn.status', Constant::Expired]);
                $query->andWhere(['!=', 'bn.status', Constant::Transferred]);
                $query->andWhere(['!=', 'bn.status', Constant::Cancelled]);
            } else {
                $query->andWhere(['bn.status' => $this->status]);
            }
        }
//        // grid filtering conditions
        $query->andFilterWhere([
            'bn.id' => $this->id,
            'bn.yard' => $this->yard,
            'bn.boat_design' => $this->boat_design,
            'bn.owner' => $this->owner,
            'bn.boat_type' => $this->boat_type,
//            'fisheries_district' => $this->fisheries_district,
//            'fisheries_division' => $this->fisheries_division,
            'bn.created' => $this->created,
            'bn.approved_time' => $this->approved_time,
            'bn.expire_date' => $this->expire_date,
        ]);
//
        $query->andFilterWhere(['like', 'bn.boat_number', $this->boat_number]);
//            ->andFilterWhere(['like', 'bn.remarks', $this->remarks])
//            ->andFilterWhere(['like', 'bn.approval_stage', $this->approval_stage]);
//        print_r($query);exit();
        return $dataProvider;
    }



    public function searchUniqueBoatNumbers($term = null)
{
    $query = BoatNumbers::find()
        ->alias('bn')
        ->select([
            'id' => 'MIN(bn.id)', // pick one id
            'text' => 'bn.boat_number'
        ])
        ->groupBy(['bn.boat_number']);

    if ($term) {
        $query->andWhere(['like', 'bn.boat_number', $term]);
    }

    return $query->asArray()->all();
}
}
