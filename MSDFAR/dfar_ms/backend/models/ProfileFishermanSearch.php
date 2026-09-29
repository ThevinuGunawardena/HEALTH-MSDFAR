<?php

namespace backend\models;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\MDivision;


/**
 * ProfileFishermanSearch represents the model behind the search form of `backend\models\ProfileFisherman`.
 */
class ProfileFishermanSearch extends ProfileFisherman
{
    /**
     * {@inheritdoc}
     */
        public $display_status;

    public function rules()
    {
        return [
            [['id', 'district', 'division', 'landing_site', 'year_recruitment', 'member_fisheries_society', 'category', 'management_area', 'status', 'display_status'], 'integer'],
            [['first_name', 'last_name', 'preferred_name_for_id', 'nic', 'passport', 'dob', 'gender', 'permanent_address', 'current_address', 'blood_group', 'mobile', 'fixed_line', 'email', 'life_isurance_no', 'civil'], 'safe'],
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
    $query = ProfileFisherman::find()
        ->alias('pf')
        ->leftJoin(
            ['pfr' => ProfileFishermanRenew::tableName()],
            'pfr.id = pf.renew_id AND pf.renew = 1'
        )
        ->with(['renewalRecord']);

    $dataProvider = new ActiveDataProvider([
        'query' => $query,
    ]);

    $dataProvider->sort->defaultOrder = ['id' => SORT_DESC];

    $this->load($params);

    if (!$this->validate()) {
        return $dataProvider;
    }

    /*
     * When a renewal exists:
     * use profile_fisherman_renew status and approval stage.
     *
     * Otherwise:
     * use profile_fisherman status and approval stage.
     */
    $displayStatusSql = '
        CASE
            WHEN pf.renew = 1 AND pfr.id IS NOT NULL
                THEN pfr.status
            ELSE pf.status
        END
    ';

    $displayApprovalStageSql = '
        CASE
            WHEN pf.renew = 1 AND pfr.id IS NOT NULL
                THEN pfr.approval_stage
            ELSE pf.approval_stage
        END
    ';

    /*
     * Pending approvals.
     * District and division remain from the main ProfileFisherman table.
     * Approval stage comes from renewal table when a renewal exists.
     */
    if ($this->display_status == Constant::Pending) {

        if (UserTypeUtil::hasType(Constant::FI)) {
            $query->andWhere([
                'pf.division' => Yii::$app->session->get('officer_division'),
            ])->andWhere(
                $displayApprovalStageSql . ' = :approvalStage',
                [':approvalStage' => Constant::FI]
            );
        }

        if (UserTypeUtil::hasType(Constant::AD)) {
            $query->andWhere([
                'pf.district' => Yii::$app->session->get('officer_district'),
            ])->andWhere(
                $displayApprovalStageSql . ' = :approvalStage',
                [':approvalStage' => Constant::AD]
            );
        }

        if (UserTypeUtil::hasType(Constant::DFI)) {
            $query->andWhere([
                'pf.district' => Yii::$app->session->get('officer_district'),
            ])->andWhere(
                $displayApprovalStageSql . ' = :approvalStage',
                [':approvalStage' => Constant::DFI]
            );
        }

        if (UserTypeUtil::hasType(Constant::DO)) {
            $query->andWhere([
                'pf.district' => Yii::$app->session->get('officer_district'),
            ])->andWhere(
                $displayApprovalStageSql . ' = :approvalStage',
                [':approvalStage' => Constant::DO]
            );
        }

    } elseif ($this->display_status == Constant::InProgress) {

        $query->andWhere(
            '(
                ' . $displayStatusSql . '
            ) NOT IN (
                :active,
                :inactive,
                :expired,
                :transferred,
                :cancelled
            )',
            [
                ':active' => Constant::Active,
                ':inactive' => Constant::Inactive,
                ':expired' => Constant::Expired,
                ':transferred' => Constant::Transferred,
                ':cancelled' => Constant::Cancelled,
            ]
        );

    } elseif ($this->display_status !== null && $this->display_status !== '') {

        $query->andWhere(
            '(' . $displayStatusSql . ') = :displayStatus',
            [':displayStatus' => $this->display_status]
        );
    }

    $query->andFilterWhere([
        'pf.id' => $this->id,
        'pf.dob' => $this->dob,
        'pf.district' => $this->district,
        'pf.division' => $this->division,
        'pf.landing_site' => $this->landing_site,
        'pf.year_recruitment' => $this->year_recruitment,
        'pf.member_fisheries_society' => $this->member_fisheries_society,
        'pf.category' => $this->category,
        'pf.management_area' => $this->management_area,
    ]);

    $query->andFilterWhere(['like', 'pf.first_name', $this->first_name])
        ->andFilterWhere(['like', 'pf.last_name', $this->last_name])
        ->andFilterWhere(['like', 'pf.preferred_name_for_id', $this->preferred_name_for_id])
        ->andFilterWhere(['like', 'pf.nic', $this->nic])
        ->andFilterWhere(['like', 'pf.passport', $this->passport])
        ->andFilterWhere(['like', 'pf.gender', $this->gender])
        ->andFilterWhere(['like', 'pf.permanent_address', $this->permanent_address])
        ->andFilterWhere(['like', 'pf.current_address', $this->current_address])
        ->andFilterWhere(['like', 'pf.blood_group', $this->blood_group])
        ->andFilterWhere(['like', 'pf.mobile', $this->mobile])
        ->andFilterWhere(['like', 'pf.fixed_line', $this->fixed_line])
        ->andFilterWhere(['like', 'pf.email', $this->email])
        ->andFilterWhere(['like', 'pf.life_isurance_no', $this->life_isurance_no])
        ->andFilterWhere(['like', 'pf.civil', $this->civil]);

    return $dataProvider;
}

    public function fishermanReport($from, $to, $district = "All")
    {
        $query = ProfileFisherman::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->where(["status" => Constant::Active]);
        if ($district != "All" && $from != null && $to != null) {
            $query->andWhere(["district" => $district]);
            $query->andWhere(['between', 'created', $from, $to]);

        }


        return $dataProvider;
    }

    public function idPrintedReport($from, $to, $district, $status = null)
{
    $query = ProfileFisherman::find();

    $dataProvider = new ActiveDataProvider([
        'query' => $query,
    ]);

    // Status filter
    if ($status !== null && $status !== '') {
        $query->andWhere(['status' => $status]);
    } else {
        $query->andWhere(['status' => Constant::Active]);
    }

    // include printed = 1 and printed = 2
    $query->andWhere(['in', 'printed', [1, 2]]);

    if ($district != "All") {
        $query->andWhere(['district' => $district]);
    }

    if ($from != null && $to != null) {
        $query->andWhere([
            'or',
            [
                'and',
                ['printed' => 1],
                ['between', 'created', $from, $to]
            ],
            [
                'and',
                ['printed' => 2],
                ['between', 'printed_date', $from, $to]
            ]
        ]);
    }

    return $dataProvider;
}

    public function idNotPrintedReport()
{
    $query = ProfileFisherman::find();

    $officerProfile = ProfileOfficer::findOne(Yii::$app->user->identity->profile_id);
    $division = $officerProfile->division;
    $district = $officerProfile->district;
    $officertype = Yii::$app->user->identity->type;

    // Subquery to get active divisions
    $activeDivisions = MDivision::find()
        ->select('id')
        ->where(['status' => 1]);

    $query->where(["status" => Constant::Active])
        ->andWhere(['printed' => 0])
        ->andWhere(['division' => $activeDivisions]) // only active divisions
        ->andWhere([
            '>',
            new \yii\db\Expression('DATE(approved_time)'),
            '2026-03-31'
        ]);

    if ($officertype == 2) {
        $query->andWhere(['division' => $division])
              ->andWhere(['district' => $district]);
    } elseif ($officertype == 3 || $officertype == 224) {
        $query->andWhere(['district' => $district]);
    }

    $dataProvider = new ActiveDataProvider([
        'query' => $query,
    ]);

    return $dataProvider;
}
}
