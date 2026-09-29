<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class ELogReportSearch extends ELog
{
    public $arrival_harbour_name;
    public $departure_harbour_name;

    // Date range fields
    public $arrival_date_from;
    public $arrival_date_to;
    public $departure_date_from;
    public $departure_date_to;

    public function rules()
    {
        return [
            [
                [
                    'vessel_id',
                    'skipper_id',
                    'arrival_harbour_name',
                    'departure_harbour_name',
                    'log_sheet_number',
                    'log_book_no',
                    'arrival_date_from',
                    'arrival_date_to',
                    'departure_date_from',
                    'departure_date_to',
                ],
                'safe'
            ],
            [['approve'], 'integer'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params, $officerHarbour = null)
    {
        $query = ELog::find()
            ->joinWith([
                'arrivalHarbour ah',
                'departureHarbour dh',
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Auto-filter by officer's harbour (arrival OR departure)
        if ($officerHarbour !== null) {
            $query->andWhere([
                'or',
                ['like', 'ah.Name', $officerHarbour],
                ['like', 'dh.Name', $officerHarbour],
            ]);
        }

        if ($this->vessel_id) {
            $query->andWhere(['like', 'e_log.vessel_id', $this->vessel_id]);
        }

        if ($this->skipper_id) {
            $query->andWhere(['like', 'e_log.skipper_id', $this->skipper_id]);
        }

        if ($this->arrival_harbour_name) {
            $query->andWhere(['like', 'ah.Name', $this->arrival_harbour_name]);
        }

        if ($this->departure_harbour_name) {
            $query->andWhere(['like', 'dh.Name', $this->departure_harbour_name]);
        }

        if ($this->approve !== null && $this->approve !== '') {
            $query->andWhere(['e_log.approve' => (int) $this->approve]);
        }

        if ($this->log_sheet_number) {
            $query->andWhere(['like', 'e_log.log_sheet_number', $this->log_sheet_number]);
        }

        if ($this->log_book_no) {
            $query->andWhere(['like', 'e_log.log_book_no', $this->log_book_no]);
        }

        // ── Arrival date range ──────────────────────────────────────────────
        if ($this->arrival_date_from && $this->arrival_date_to) {
            $query->andWhere(['between', 'e_log.arrival_date', $this->arrival_date_from, $this->arrival_date_to]);
        } elseif ($this->arrival_date_from) {
            $query->andWhere(['>=', 'e_log.arrival_date', $this->arrival_date_from]);
        } elseif ($this->arrival_date_to) {
            $query->andWhere(['<=', 'e_log.arrival_date', $this->arrival_date_to]);
        }

        // ── Departure date range ────────────────────────────────────────────
        if ($this->departure_date_from && $this->departure_date_to) {
            $query->andWhere(['between', 'e_log.departure_date', $this->departure_date_from, $this->departure_date_to]);
        } elseif ($this->departure_date_from) {
            $query->andWhere(['>=', 'e_log.departure_date', $this->departure_date_from]);
        } elseif ($this->departure_date_to) {
            $query->andWhere(['<=', 'e_log.departure_date', $this->departure_date_to]);
        }

        return $dataProvider;
    }
}