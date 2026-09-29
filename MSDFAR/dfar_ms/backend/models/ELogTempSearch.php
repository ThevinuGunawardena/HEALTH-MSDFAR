<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class ELogTempSearch extends ELogTemp
{
    public $arrival_harbour_name;
    public $departure_harbour_name;

    public function rules()
    {
        return [
            [
                [
                    'vessel_id',
                    'gear_type',
                    'arrival_harbour_name',
                    'departure_harbour_name',
                    'arrival_date',
                    'departure_date',
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
        $query = ELogTemp::find()
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
            $query->andWhere(['like', 'e_log_temp.vessel_id', $this->vessel_id]);
        }

        if ($this->gear_type) {
            $query->andWhere(['e_log_temp.gear_type' => $this->gear_type]);
        }

        if ($this->arrival_harbour_name) {
            $query->andWhere(['like', 'ah.Name', $this->arrival_harbour_name]);
        }

        if ($this->departure_harbour_name) {
            $query->andWhere(['like', 'dh.Name', $this->departure_harbour_name]);
        }

        if ($this->approve !== null && $this->approve !== '') {
            $query->andWhere(['e_log_temp.approve' => (int) $this->approve]);
        }

        if ($this->arrival_date) {
            $query->andWhere(['arrival_date' => $this->arrival_date]);
        }

        if ($this->departure_date) {
            $query->andWhere(['departure_date' => $this->departure_date]);
        }

        return $dataProvider;
    }
}