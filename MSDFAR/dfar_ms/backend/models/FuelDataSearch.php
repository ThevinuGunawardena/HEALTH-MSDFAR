<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\FuelData;
use backend\config\Constant;
use backend\config\UserTypeUtil;
use Yii;


/**
 * FuelDataSearch represents the model behind the search form of `backend\models\FuelData`.
 */
class FuelDataSearch extends FuelData
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'boat_registration_id', 'bank_code', 'bank_branch', 'account_number', 'fuel_quota_cat',  'status'], 'integer'],
            [['renew_at'], 'safe'],
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
   public function search($params)
{
    $query = FuelData::find()
        ->alias('fd')
        ->select([
            'fd.*',
            'd.name AS district_name',
            'div.name AS division_name'
        ])
        ->leftJoin('fisherman_registerd_boat_license frbl', 'frbl.id = fd.boat_registration_id')
        ->leftJoin('profile_fisherman pf', 'pf.id = frbl.fisherman_id')
        ->leftJoin('m_fi_district d', 'd.id = pf.district')
        ->leftJoin('m_division div', 'div.id = pf.division');

    $dataProvider = new ActiveDataProvider([
        'query' => $query,
    ]);

    // ✅ SORT
    $dataProvider->sort->attributes['id'] = [
        'asc' => ['fd.id' => SORT_ASC],
        'desc' => ['fd.id' => SORT_DESC],
    ];
    $dataProvider->sort->defaultOrder = ['id' => SORT_DESC];

    // ✅ LOAD
    $this->load($params);

    if (!$this->validate()) {
        return $dataProvider;
    }

    // =========================
    // ✅ ROLE FILTER (CORRECT)
    // =========================

    if ($this->status == Constant::Pending) {

        if (UserTypeUtil::hasType(Constant::FI)) {
            $query->andWhere([
                'frbl.division' => Yii::$app->session->get("officer_division"),
                'fd.approval_stage' => Constant::FI
            ]);
        }

        if (UserTypeUtil::hasType(Constant::AD)) {
            $query->andWhere([
                'frbl.district' => Yii::$app->session->get("officer_district"),
                'fd.approval_stage' => Constant::AD
            ]);
        }

        if (UserTypeUtil::hasType(Constant::DFI)) {
            $query->andWhere([
                'frbl.district' => Yii::$app->session->get("officer_district"),
                'fd.approval_stage' => Constant::DFI
            ]);
        }

        if (UserTypeUtil::hasType(Constant::DO)) {
            $query->andWhere([
                'frbl.district' => Yii::$app->session->get("officer_district"),
                'fd.approval_stage' => Constant::DO
            ]);
        }

        if (UserTypeUtil::hasType(Constant::DM)) {
            $query->andWhere(['fd.approval_stage' => Constant::DM]);
        }

        if (UserTypeUtil::hasType(Constant::DG)) {
            $query->andWhere(['fd.approval_stage' => Constant::DG]);
        }

    } else {

        if (UserTypeUtil::hasType(Constant::FI)) {
            $query->andWhere([
                'frbl.division' => Yii::$app->session->get("officer_division")
            ]);
        }

        if (
            UserTypeUtil::hasType(Constant::AD) ||
            UserTypeUtil::hasType(Constant::DFI) ||
            UserTypeUtil::hasType(Constant::DO)
        ) {
            $query->andWhere([
                'frbl.district' => Yii::$app->session->get("officer_district")
            ]);
        }
    }

    // =========================
    // ✅ STATUS FILTER (FIXED)
    // =========================

    if ($this->status == Constant::InProgress) {

        $query->andWhere(['NOT IN', 'fd.status', [
            Constant::Active,
            Constant::Inactive,
            Constant::Expired,
            Constant::Transferred,
            Constant::Cancelled
        ]]);

    } else if ($this->status == Constant::Pending) {


    } else if ($this->status != "") {

        $query->andWhere(['fd.status' => $this->status]);
    }

    // =========================
    // ✅ GRID FILTER (FIXED)
    // =========================

    $query->andFilterWhere([
        'fd.id' => $this->id,
        'fd.boat_registration_id' => $this->boat_registration_id,
    ]);

    return $dataProvider;
}
}
