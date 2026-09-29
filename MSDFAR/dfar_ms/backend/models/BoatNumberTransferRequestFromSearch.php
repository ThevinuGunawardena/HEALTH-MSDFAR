<?php

namespace backend\models;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * BoatNumberTransferRequestFromSearch represents the model behind the search form of `backend\models\BoatNumberTransferRequest`.
 */
class BoatNumberTransferRequestFromSearch extends BoatNumberTransferRequest
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'new_owner', 'new_landing_district', 'new_landing_site', 'status'], 'integer'],
            [['witness_name', 'witness_address', 'witness_nic', 'witness_sign_date', 'remark', 'approval_stage', 'created', 'approved_time'], 'safe'],
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
        $query = BoatNumberTransferRequest::find()->alias('bt')->select('bt.*')
            ->joinWith(['boatNumber bn']);

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
                $query->where(["bn.fisheries_division" => Yii::$app->session->get("officer_division"), 'bt.approval_stage' => Constant::FI]);
            }
            if (UserTypeUtil::hasType(Constant::AD)) {
                $query->where(["bn.fisheries_district" => Yii::$app->session->get("officer_district"), 'bt.approval_stage' => Constant::AD]);
            }

            if (UserTypeUtil::hasType(Constant::DFI)) {
                $query->where(["bn.fisheries_district" => Yii::$app->session->get("officer_district"), 'bt.approval_stage' => Constant::DFI]);
            }
            if (UserTypeUtil::hasType(Constant::DO)) {
                $query->where(["bn.fisheries_district" => Yii::$app->session->get("officer_district"), 'bt.approval_stage' => Constant::DO]);
            }
            if (UserTypeUtil::hasType(Constant::DM)) {
                $query->where(['bt.approval_stage' => Constant::DM]);
            }
            if (UserTypeUtil::hasType(Constant::DG)) {
                $query->where(['bt.approval_stage' => Constant::DG]);
            }
        } else {
            if (UserTypeUtil::hasType(Constant::FI)) {
                $query->where(["bn.division" => Yii::$app->session->get("officer_division")]);
            }
            if (UserTypeUtil::hasType(Constant::AD)) {
                $query->where(["bn.fisheries_district" => Yii::$app->session->get("officer_district")]);
            }

            if (UserTypeUtil::hasType(Constant::DFI)) {
                $query->where(["bn.fisheries_district" => Yii::$app->session->get("officer_district")]);
            }
            if (UserTypeUtil::hasType(Constant::DO)) {
                $query->where(["bn.fisheries_district" => Yii::$app->session->get("officer_district")]);
            }
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'new_owner' => $this->new_owner,
            'new_landing_district' => $this->new_landing_district,
            'new_landing_site' => $this->new_landing_site,
            'witness_sign_date' => $this->witness_sign_date,
            'bt.status' => $this->status,
            'created' => $this->created,
            'approved_time' => $this->approved_time,
        ]);

        $query->andFilterWhere(['like', 'witness_name', $this->witness_name])
            ->andFilterWhere(['like', 'witness_address', $this->witness_address])
            ->andFilterWhere(['like', 'witness_nic', $this->witness_nic])
            ->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['like', 'bt.approval_stage', $this->approval_stage]);

        return $dataProvider;
    }
}
