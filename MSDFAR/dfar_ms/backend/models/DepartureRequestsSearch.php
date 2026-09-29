<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DepartureRequestsSearch represents the model behind the search form of `backend\models\DepartureRequests`.
 */
class DepartureRequestsSearch extends DepartureRequests
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'longline_hooks'], 'integer'],
            [['boat_no', 'boat_name', 'owner', 'contact_no', 'email', 'skipper', 'skipper_no', 'skipper_nic', 'district', 'harbor', 'fishing_area', 'national_license_no', 'hs_license_no', 'vms', 'agree', 'req_date_time', 'user', 'action_date', 'approve', 'remarks', 'water_bot', 'mcs', 'frequency', 'vms_code', 'manual', 'arrivalPort', 'arrivalDate', 'arrTime'], 'safe'],
            [['length_longline', 'length_gillnet', 'length_ringnet', 'mesh_gillnet', 'mesh_ringnet'], 'number'],
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
    public function search($params, $allIsland)
    {
        $query = DepartureRequests::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->sort->defaultOrder = ['id' => SORT_DESC];
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($allIsland == 1) {
//            if (UserTypeUtil::hasType(Constant::HARBOUR_OFFICER) && Util::editPermission()) {
            $query->where(["approve" => ['A', 'R']]);
//            }
        } else {
            $query->where(["harbor" => Yii::$app->session->get("officer_harbour")]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'length_longline' => $this->length_longline,
            'length_gillnet' => $this->length_gillnet,
            'length_ringnet' => $this->length_ringnet,
            'longline_hooks' => $this->longline_hooks,
            'mesh_gillnet' => $this->mesh_gillnet,
            'mesh_ringnet' => $this->mesh_ringnet,
            'req_date_time' => $this->req_date_time,

        ]);

        $query->andFilterWhere(['like', 'boat_no', $this->boat_no])
            ->andFilterWhere(['like', 'boat_name', $this->boat_name])
            ->andFilterWhere(['like', 'owner', $this->owner])
            ->andFilterWhere(['like', 'contact_no', $this->contact_no])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'skipper', $this->skipper])
            ->andFilterWhere(['like', 'skipper_no', $this->skipper_no])
            ->andFilterWhere(['like', 'skipper_nic', $this->skipper_nic])
            ->andFilterWhere(['like', 'district', $this->district])
            ->andFilterWhere(['like', 'harbor', $this->harbor])
            ->andFilterWhere(['like', 'fishing_area', $this->fishing_area])
            ->andFilterWhere(['like', 'national_license_no', $this->national_license_no])
            ->andFilterWhere(['like', 'hs_license_no', $this->hs_license_no])
            ->andFilterWhere(['like', 'vms', $this->vms])
            ->andFilterWhere(['like', 'agree', $this->agree])
            ->andFilterWhere(['like', 'user', $this->user])
            ->andFilterWhere(['like', 'approve', $this->approve])
            ->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'water_bot', $this->water_bot])
            ->andFilterWhere(['like', 'mcs', $this->mcs])
            ->andFilterWhere(['like', 'frequency', $this->frequency])
            ->andFilterWhere(['like', 'vms_code', $this->vms_code])
            ->andFilterWhere(['like', 'manual', $this->manual])
            ->andFilterWhere(['like', 'arrivalPort', $this->arrivalPort])
            ->andFilterWhere(['like', 'arrivalDate', $this->arrivalDate])
            ->andFilterWhere(['like', 'arrTime', $this->arrTime])
            ->andFilterWhere(['like', 'action_date', $this->action_date]);

        return $dataProvider;
    }
}
