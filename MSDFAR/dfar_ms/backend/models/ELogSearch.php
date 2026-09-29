<?php
namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class ELogSearch extends ELog
{
    public $arrival_harbour_name;
    public $departure_harbour_name;

    public function rules()
    {
        return [
            [
                [
                    'vessel_id',
                    'skipper_id',
                    'arrival_harbour_name',
                    'departure_harbour_name',
                    'arrival_date',
                    'departure_date',
                    'log_sheet_number',
                    'log_book_no'
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
        if ($this->arrival_date) {
            $query->andWhere(['arrival_date' => $this->arrival_date]);
        }

        if ($this->departure_date) {
            $query->andWhere(['departure_date' => $this->departure_date]);
        }

        if ($this->log_sheet_number) {
            $query->andWhere([
                'like',
                'e_log.log_sheet_number',
                $this->log_sheet_number
            ]);
        }

        if ($this->log_book_no) {
            $query->andWhere([
                'like',
                'e_log.log_book_no',
                $this->log_book_no
            ]);
        }

        return $dataProvider;
    }
}