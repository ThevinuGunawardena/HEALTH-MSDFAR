<?php

namespace backend\models;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * FishermanRegisterdBoatSearch represents the model behind the search form of `backend\models\FishermanRegisterdBoat`.
 */
class FishermanRegisterdBoatSearch extends FishermanRegisterdBoat
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'boat_number_id', 'fisherman_id'], 'integer'],
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
        $query = FishermanRegisterdBoat::find();

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
            'boat_number_id' => $this->boat_number_id,
            'fisherman_id' => $this->fisherman_id,
        ]);

        return $dataProvider;
    }

    public function searchMea($params)
    {
        $query = FishermanRegisterdBoat::find()->alias('br')->select('br.*')
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
        if (UserTypeUtil::hasType(Constant::FI)) {
            $query->where(["br.division" => Yii::$app->session->get("officer_division")]);
        }
//        if (UserTypeUtil::hasType(Constant::AD)) {
//            $query->where(["br.district" => Yii::$app->session->get("officer_district")]);
//        }
//        if (UserTypeUtil::hasType(Constant::MEA)) {
//            $query->where(["br.district" => Yii::$app->session->get("officer_district")]);
//        }
        if (UserTypeUtil::hasType(Constant::DFI)) {
            $query->where(["br.district" => Yii::$app->session->get("officer_district")]);
        }
        $query->andWhere(["br.status" => Constant::Active])->orWhere(["br.status" => Constant::Pending]);
        // grid filtering conditions
        $query->andFilterWhere([
            'br.id' => $this->id,

        ]);

        $query->andFilterWhere(['like', 'fm.fisherman_uid', $this->fisherman_id])
            ->andFilterWhere(['like', 'bn.boat_number', $this->boat_number_id]);

        return $dataProvider;
    }
}
