<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ProfileFishermanRenew;

/**
 * ProfileFishermanRenewSearch represents the model behind the search form of `backend\models\ProfileFishermanRenew`.
 */
class ProfileFishermanRenewSearch extends ProfileFishermanRenew
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fisherman_id', 'district', 'division', 'status', 'renew', 'printed', 'privacy_policy'], 'integer'],
            [['fisherman_uid', 'approval_stage', 'created', 'approved_time', 'expire_date', 'printed_date'], 'safe'],
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
    public function search($params, $formName = null)
    {
        $query = ProfileFishermanRenew::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'fisherman_id' => $this->fisherman_id,
            'district' => $this->district,
            'division' => $this->division,
            'status' => $this->status,
            'created' => $this->created,
            'approved_time' => $this->approved_time,
            'expire_date' => $this->expire_date,
            'renew' => $this->renew,
            'printed' => $this->printed,
            'printed_date' => $this->printed_date,
            'privacy_policy' => $this->privacy_policy,
        ]);

        $query->andFilterWhere(['like', 'fisherman_uid', $this->fisherman_uid])
            ->andFilterWhere(['like', 'approval_stage', $this->approval_stage]);

        return $dataProvider;
    }
}
