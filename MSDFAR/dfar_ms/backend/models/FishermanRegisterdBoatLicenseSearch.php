<?php

namespace backend\models;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * FishermanRegisterdBoatLicenseSearch represents the model behind the search form of `backend\models\FishermanRegisterdBoatLicense`.
 */
class FishermanRegisterdBoatLicenseSearch extends FishermanRegisterdBoatLicense
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nid', 'id', 'boat_number_id', 'fisherman_id', 'district', 'division', 'landing_site', 'status', 'renew'], 'integer'],
            [['insurance_no', 'call_sign_no', 'witness_name', 'witness_address', 'witness_nic', 'witness_singing_date', 'how_propelled', 'engine_make', 'fuel_type', 'engine_type', 'engine_serial_number', 'communication_equipment', 'fishing_equipment', 'navigation_equipment', 'date_of_construction', 'date_of_first_registration', 'mea_report', 'approval_stage', 'created', 'approved_time', 'expire_date'], 'safe'],
            [['engine_horsepower'], 'number'],
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
     * @return ActiveDataProvider
     */
    public function search($params, $id)
    {
        $query = FishermanRegisterdBoatLicense::find();

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
        $query->where(['id' => $id]);
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
            'nid' => $this->nid,
            'boat_number_id' => $this->boat_number_id,
            'fisherman_id' => $this->fisherman_id,
            'district' => $this->district,
            'division' => $this->division,
            'landing_site' => $this->landing_site,
            'witness_singing_date' => $this->witness_singing_date,
            'engine_horsepower' => $this->engine_horsepower,
            'date_of_construction' => $this->date_of_construction,
            'date_of_first_registration' => $this->date_of_first_registration,
            'created' => $this->created,
            'approved_time' => $this->approved_time,
            'expire_date' => $this->expire_date,
            'renew' => $this->renew,
        ]);

        $query->andFilterWhere(['like', 'insurance_no', $this->insurance_no])
            ->andFilterWhere(['like', 'call_sign_no', $this->call_sign_no])
            ->andFilterWhere(['like', 'witness_name', $this->witness_name])
            ->andFilterWhere(['like', 'witness_address', $this->witness_address])
            ->andFilterWhere(['like', 'witness_nic', $this->witness_nic])
            ->andFilterWhere(['like', 'how_propelled', $this->how_propelled])
            ->andFilterWhere(['like', 'engine_make', $this->engine_make])
            ->andFilterWhere(['like', 'fuel_type', $this->fuel_type])
            ->andFilterWhere(['like', 'engine_type', $this->engine_type])
            ->andFilterWhere(['like', 'engine_serial_number', $this->engine_serial_number])
            ->andFilterWhere(['like', 'communication_equipment', $this->communication_equipment])
            ->andFilterWhere(['like', 'fishing_equipment', $this->fishing_equipment])
            ->andFilterWhere(['like', 'navigation_equipment', $this->navigation_equipment])
            ->andFilterWhere(['like', 'mea_report', $this->mea_report])
            ->andFilterWhere(['like', 'approval_stage', $this->approval_stage]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     * @return ActiveDataProvider
     */
    public function searchLicense($params)
    {
        $query = FishermanRegisterdBoatLicense::find()->alias('br')->select('br.*')
            ->joinWith(['boatNumber bn'])
            ->joinWith(['fisherman fm']);
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
                $query->where(["br.division" => Yii::$app->session->get("officer_division"), 'br.approval_stage' => Constant::FI]);
            }
            if (UserTypeUtil::hasType(Constant::AD)) {
                $query->where(["br.district" => Yii::$app->session->get("officer_district"), 'br.approval_stage' => Constant::AD]);
            }
            if (UserTypeUtil::hasType(Constant::MEA)) {
                $query->where(["br.district" => Yii::$app->session->get("officer_district"), 'br.approval_stage' => Constant::MEA]);
            }
            if (UserTypeUtil::hasType(Constant::DFI)) {
                $query->where(["br.district" => Yii::$app->session->get("officer_district"), 'br.approval_stage' => Constant::DFI]);
            }
            if (UserTypeUtil::hasType(Constant::DO)) {
                $query->where(["br.district" => Yii::$app->session->get("officer_district"), 'br.approval_stage' => Constant::DO]);
            }
            if (UserTypeUtil::hasType(Constant::DM)) {
                $query->where(['br.approval_stage' => Constant::DM]);
            }
            if (UserTypeUtil::hasType(Constant::DG)) {
                $query->where(['br.approval_stage' => Constant::DG]);
            }
        } else {
            if (UserTypeUtil::hasType(Constant::FI)) {
                $query->where(["br.division" => Yii::$app->session->get("officer_division")]);
            }
            if (UserTypeUtil::hasType(Constant::AD)) {
                $query->where(["br.district" => Yii::$app->session->get("officer_district")]);
            }
            if (UserTypeUtil::hasType(Constant::MEA)) {
                $query->where(["br.district" => Yii::$app->session->get("officer_district")]);
            }
            if (UserTypeUtil::hasType(Constant::DFI)) {
                $query->where(["br.district" => Yii::$app->session->get("officer_district")]);
            }
            if (UserTypeUtil::hasType(Constant::DO)) {
                $query->where(["br.district" => Yii::$app->session->get("officer_district")]);
            }
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'nid' => $this->nid,
            'boat_number_id' => $this->boat_number_id,
            'fisherman_id' => $this->fisherman_id,
            'district' => $this->district,
            'division' => $this->division,
            'landing_site' => $this->landing_site,
            'witness_singing_date' => $this->witness_singing_date,
            'engine_horsepower' => $this->engine_horsepower,
            'date_of_construction' => $this->date_of_construction,
            'date_of_first_registration' => $this->date_of_first_registration,
            'br.status' => $this->status,
            'created' => $this->created,
            'approved_time' => $this->approved_time,
            'expire_date' => $this->expire_date,
            'renew' => $this->renew,
        ]);

        $query->andFilterWhere(['like', 'insurance_no', $this->insurance_no])
            ->andFilterWhere(['like', 'call_sign_no', $this->call_sign_no])
            ->andFilterWhere(['like', 'witness_name', $this->witness_name])
            ->andFilterWhere(['like', 'witness_address', $this->witness_address])
            ->andFilterWhere(['like', 'witness_nic', $this->witness_nic])
            ->andFilterWhere(['like', 'how_propelled', $this->how_propelled])
            ->andFilterWhere(['like', 'engine_make', $this->engine_make])
            ->andFilterWhere(['like', 'fuel_type', $this->fuel_type])
            ->andFilterWhere(['like', 'engine_type', $this->engine_type])
            ->andFilterWhere(['like', 'engine_serial_number', $this->engine_serial_number])
            ->andFilterWhere(['like', 'communication_equipment', $this->communication_equipment])
            ->andFilterWhere(['like', 'fishing_equipment', $this->fishing_equipment])
            ->andFilterWhere(['like', 'navigation_equipment', $this->navigation_equipment])
            ->andFilterWhere(['like', 'mea_report', $this->mea_report])
            ->andFilterWhere(['like', 'br.approval_stage', $this->approval_stage]);

        return $dataProvider;
    }

}
