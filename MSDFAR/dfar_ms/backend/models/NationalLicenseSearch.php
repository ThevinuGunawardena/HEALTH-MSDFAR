<?php

namespace backend\models;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * NationalLicenseSearch represents the model behind the search form of `backend\models\NationalLicense`.
 */
class NationalLicenseSearch extends NationalLicense
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fisherman_id', 'boat_registration_id', 'fisheries_district', 'division', 'main_gear_type', 'landing_site', 'status', 'renew'], 'integer'],
            [['license_number', 'division_gear_types', 'approval_stage', 'created', 'approved_time', 'expire_date'], 'safe'],
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
        $query = NationalLicense::find();

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
        if (UserTypeUtil::hasType(Constant::FI)) {
            $query->where(["division" => Yii::$app->session->get("officer_division")]);
        }
        if (UserTypeUtil::hasType(Constant::AD)) {
            $query->where(["fisheries_district" => Yii::$app->session->get("officer_district")]);
        }
        if (UserTypeUtil::hasType(Constant::DFI)) {
            $query->where(["fisheries_district" => Yii::$app->session->get("officer_district")]);
        }
        if (UserTypeUtil::hasType(Constant::DO)) {
            $query->where(["fisheries_district" => Yii::$app->session->get("officer_district")]);
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
            'fisheries_district' => $this->fisheries_district,
            'division' => $this->division,
            'main_gear_type' => $this->main_gear_type,
            'landing_site' => $this->landing_site,
            'created' => $this->created,
            'approved_time' => $this->approved_time,
            'expire_date' => $this->expire_date,
            'renew' => $this->renew,
        ]);

        $query->andFilterWhere(['like', 'license_number', $this->license_number])
            ->andFilterWhere(['like', 'division_gear_types', $this->division_gear_types])
            ->andFilterWhere(['like', 'approval_stage', $this->approval_stage]);

        return $dataProvider;
    }


    public function licenseReport($from, $to, $district = "All")
    {
        $query = NationalLicense::find();
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
