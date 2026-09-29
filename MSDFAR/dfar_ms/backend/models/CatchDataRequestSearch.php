<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\CatchDataRequest;

class CatchDataRequestSearch extends CatchDataRequest
{
    public $boat_number; // 🔥 for searching by boat number

    public function rules()
    {
        return [
            [['id', 'boat_registration_id', 'unloading_harbour', 'fishing_gear_type', 'created_by'], 'integer'],
            [['landing_date', 'log_book_no', 'log_book_page_no', 'created_at', 'boat_number'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = CatchDataRequest::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // ✅ IMPORTANT FIX (removed formName issue)
        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // ✅ NORMAL FILTERS
        $query->andFilterWhere([
            'id' => $this->id,
            'landing_date' => $this->landing_date,
            'boat_registration_id' => $this->boat_registration_id,
            'unloading_harbour' => $this->unloading_harbour,
            'fishing_gear_type' => $this->fishing_gear_type,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', 'log_book_no', $this->log_book_no])
              ->andFilterWhere(['like', 'log_book_page_no', $this->log_book_page_no]);

        // ==================================================
        // 🔥 BOAT NUMBER SEARCH (OPTIONAL)
        // ==================================================
        if (!empty($this->boat_number)) {

            $boatIds = \backend\models\BoatNumbers::find()
                ->select('id')
                ->where(['like', 'boat_number', $this->boat_number])
                ->column();

            if (!empty($boatIds)) {
                $query->andWhere(['boat_registration_id' => $boatIds]);
            } else {
                $query->andWhere(['boat_registration_id' => 0]);
            }
        }

        return $dataProvider;
    }
}