<?php

namespace backend\models;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use Yii;

/**
 * LeaveSearch represents the model behind the search form of `backend\models\Leave`.
 *
 * Scoping rules:
 *  - Regular employees        → only see their own leave records
 *  - Approvers (DG / ITD / district AD) → see all records in their scope;
 *                               can filter by NIC or role
 *  - Any user with $ownOnly   → forced to their own records (the "Request Leave"
 *                               personal view, mode=my), even if they are an approver
 *
 * NOTE: the caller (controller) is responsible for applying the approver's
 * scope condition (e.g. DG → Director requests only) via
 * $dataProvider->query->andWhere($scope).
 */
class LeaveSearch extends Leave
{
    /**
     * Extra search attributes for approvers only.
     * These are NOT columns in the `leave` table — they join to `user`.
     */
    public $employee_nic;   // filter by user.nic
    public $employee_type;  // filter by leave.user_type (role)

    /**
     * When true, the search is forced to the current user's own records,
     * even if the logged-in user is an approver (DG / ITD / AD).
     * Set by the controller for the personal "My Leave Requests" view
     * (Request Leave tab, mode=my).
     */
    public $ownOnly = false;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'total_days', 'approved_by'], 'integer'],
            [['leave_type', 'start_date', 'end_date', 'reason',
              'status', 'remarks', 'approved_at', 'created_at'], 'safe'],
            // Approver extra filters
            [['employee_nic', 'employee_type'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied.
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        // The DG, the IT Director and each district's AD are the approvers
        // under workplace-based routing — each may browse/filter records
        // beyond their own (their exact scope is applied by the controller
        // on top of this query). Directors no longer approve anyone.
        $isApprover = UserTypeUtil::isDG()
            || UserTypeUtil::hasType(Constant::ITD)
            || UserTypeUtil::hasType(Constant::AD);

        // Whether this search runs in "all employees" mode
        $showAll = $isApprover && !$this->ownOnly;

        // Use 'lv' alias to avoid backtick conflict with reserved word `leave`
        $query = Leave::find()->alias('lv');

        // Approvers can search by officer name or NIC, so join the user
        // table (for NIC) and the officer profile (for first/last name).
        if ($showAll) {
            $query->leftJoin('user', 'user.id = lv.user_id');
            $query->leftJoin('profile_officer', 'profile_officer.id = user.profile_id');
        }

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => ['pageSize' => 15],
            'sort'       => [
                'defaultOrder' => ['created_at' => SORT_DESC],
                'attributes'   => [
                    'id',
                    'leave_type',
                    'status',
                    'start_date',
                    'end_date',
                    'total_days',
                    'created_at',
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // ── SCOPE: own records only (employees, or approver in mode=my) ──
        if (!$showAll) {
            $query->andWhere(['lv.user_id' => Yii::$app->user->id]);
        }

        // ── Standard filters (available to everyone) ─────────────────
        $query->andFilterWhere([
            'lv.id'          => $this->id,
            'lv.user_id'     => $this->user_id,
            'lv.total_days'  => $this->total_days,
            'lv.approved_by' => $this->approved_by,
        ]);

        $query->andFilterWhere(['like', 'lv.leave_type', $this->leave_type])
              ->andFilterWhere(['like', 'lv.status',     $this->status])
              ->andFilterWhere(['like', 'lv.reason',     $this->reason])
              ->andFilterWhere(['>=',   'lv.start_date', $this->start_date ?: null])
              ->andFilterWhere(['<=',   'lv.end_date',   $this->end_date   ?: null]);

        // ── Approver extra filters ───────────────────────────────────
        if ($showAll) {
            // One box matches officer NIC OR name (first, last, or full).
            // Built as explicit SQL with a single bound parameter so it
            // behaves consistently across Yii versions (the `like` builder
            // can mishandle a CONCAT expression as a column).
            $q = trim((string) $this->employee_nic);
            if ($q !== '') {
                $query->andWhere(
                    "(`user`.`nic` LIKE :officerQ"
                    . " OR `profile_officer`.`first_name` LIKE :officerQ"
                    . " OR `profile_officer`.`last_name` LIKE :officerQ"
                    . " OR CONCAT(`profile_officer`.`first_name`, ' ', `profile_officer`.`last_name`) LIKE :officerQ)",
                    [':officerQ' => '%' . $q . '%']
                );
            }
            // Exact match — a LIKE here would make type "4" also match
            // "40", "14", "999", etc.
            $query->andFilterWhere(['lv.user_type' => $this->employee_type]);
        }

        return $dataProvider;
    }
}