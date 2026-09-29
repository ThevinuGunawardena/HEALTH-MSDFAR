<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * BoatDesignSearch represents the model behind the search form of `backend\models\BoatDesign`.
 */
class BoatDesignSearch extends BoatDesign
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'yard', 'boat_type', 'hull_material', 'engin_type', 'fi_district', 'status'], 'integer'],
            [['design_notation', 'remark'], 'safe'],
            [['length', 'width', 'height', 'draft'], 'number'],
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
        $query = BoatDesign::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'yard' => $this->yard,
            'boat_type' => $this->boat_type,
            'hull_material' => $this->hull_material,
            'engin_type' => $this->engin_type,
            'fi_district' => $this->fi_district,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,
            'draft' => $this->draft,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'design_notation', $this->design_notation])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
