<?php

namespace backend\models;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * HighseasLicenseSearch represents the model behind the search form of `backend\models\HighseasLicense`.
 */
class HighseasLicenseSearch extends HighseasLicense
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fisherman_id', 'boat_registration_id', 'skipper_id', 'no_if_crew_members', 'main_gear_type', 'fishing_gear_type', 'district', 'division', 'landing_harbour', 'status', 'renew'], 'integer'],
            [['license_number', 'prevouse_boat_flag', 'unloading_sites', 'approval_stage', 'created', 'approved_time', 'expire_date'], 'safe'],
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
        $query = HighseasLicense::find();

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
                $query->where(["division" => Yii::$app->session->get("officer_division"), 'approval_stage' => Constant::FI]);
            }
            if (UserTypeUtil::hasType(Constant::AD)) {
                $query->where(["district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::AD]);
            }
            if (UserTypeUtil::hasType(Constant::DFI)) {
                $query->where(["district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::DFI]);
            }
            if (UserTypeUtil::hasType(Constant::DO)) {
                $query->where(["district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::DO]);
            }
            if (UserTypeUtil::hasType(Constant::DM)) {
                $query->where(['approval_stage' => Constant::DM]);
            }
            if (UserTypeUtil::hasType(Constant::DG)) {
                $query->where(['approval_stage' => Constant::DG]);
            }
        } else {
            if (UserTypeUtil::hasType(Constant::FI)) {
                $query->where(["division" => Yii::$app->session->get("officer_division")]);
            }
            if (UserTypeUtil::hasType(Constant::AD)) {
                $query->where(["district" => Yii::$app->session->get("officer_district")]);
            }
            if (UserTypeUtil::hasType(Constant::DFI)) {
                $query->where(["district" => Yii::$app->session->get("officer_district")]);
            }
            if (UserTypeUtil::hasType(Constant::DO)) {
                $query->where(["district" => Yii::$app->session->get("officer_district")]);
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
            'boat_registration_id' => $this->boat_registration_id,
            'skipper_id' => $this->skipper_id,
            'no_if_crew_members' => $this->no_if_crew_members,
            'main_gear_type' => $this->main_gear_type,
            'fishing_gear_type' => $this->fishing_gear_type,
            'district' => $this->district,
            'division' => $this->division,
            'landing_harbour' => $this->landing_harbour,
            'created' => $this->created,
            'approved_time' => $this->approved_time,
            'expire_date' => $this->expire_date,
            'renew' => $this->renew,
        ]);

        $query->andFilterWhere(['like', 'license_number', $this->license_number])
            ->andFilterWhere(['like', 'prevouse_boat_flag', $this->prevouse_boat_flag])
            ->andFilterWhere(['like', 'unloading_sites', $this->unloading_sites])
            ->andFilterWhere(['like', 'approval_stage', $this->approval_stage]);

        return $dataProvider;
    }
}
