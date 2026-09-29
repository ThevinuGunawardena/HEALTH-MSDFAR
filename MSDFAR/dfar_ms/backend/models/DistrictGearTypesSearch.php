<?php

namespace backend\models;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\DistrictGearTypes;

/**
 * DistrictGearTypesSearch represents the model behind the search form of `backend\models\DistrictGearTypes`.
 */
class DistrictGearTypesSearch extends DistrictGearTypes
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'gear_type', 'status', 'division'], 'integer'],
            [['extra', 'fishing_time_periods', 'fishing_time_durations', 'fish_species'], 'safe'],
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
        $query = DistrictGearTypes::find();

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
        if (UserTypeUtil::hasType(Constant::FI)) {
            $query->where(["division"=>Yii::$app->session->get("officer_division")]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'gear_type' => $this->gear_type,
            'status' => $this->status,
            'division' => $this->division,
        ]);

        $query->andFilterWhere(['like', 'extra', $this->extra])
            ->andFilterWhere(['like', 'fishing_time_periods', $this->fishing_time_periods])
            ->andFilterWhere(['like', 'fishing_time_durations', $this->fishing_time_durations])
            ->andFilterWhere(['like', 'fish_species', $this->fish_species]);

        return $dataProvider;
    }
}
