<?php

namespace backend\models;

use backend\config\Constant;
use backend\services\Util;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ApplicationexportnaklaSearch represents the model behind the search form of `backend\models\Applicationexportnakla`.
 */
class ApplicationexportnaklaSearch extends Applicationexportnakla
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name', 'permanent_address', 'nic_number', 'purchase_place', 'export_countries', 'contact_value', 'document', 'supporting_document', 'approval_stage', 'created', 'approved_time', 'expire_date'], 'safe'],
            [['telephone_number', 'fax_number', 'id', 'company', 'business_reg_number', 'previouspermit_exported_quantity_pieces', 'export_quantity_pieces', 'previouspermit_exported_quantity_kg', 'export_quantity_kg', 'contact_number', 'previouspermit_exported_quantity', 'export_quantity', 'status'], 'integer'],
            [['tnc'], 'string'],
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
        $query = Applicationexportnakla::find();

        // add conditions that should always apply here
        if (Yii::$app->user->identity->type == Constant::EXPORT_COMPANY && Util::editPermission()) {
            $query->where(["company" => Yii::$app->user->identity->profile_id]);
        }
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
            'telephone_number' => $this->telephone_number,
            'fax_number' => $this->fax_number,
            'id' => $this->id,
            'company' => $this->company,
            'business_reg_number' => $this->business_reg_number,
            'previouspermit_exported_quantity_pieces' => $this->previouspermit_exported_quantity_pieces,
            'export_quantity_pieces' => $this->export_quantity_pieces,
            'previouspermit_exported_quantity_kg' => $this->previouspermit_exported_quantity_kg,
            'export_quantity_kg' => $this->export_quantity_kg,
            'contact_number' => $this->contact_number,
            'previouspermit_exported_quantity' => $this->previouspermit_exported_quantity,
            'export_quantity' => $this->export_quantity,
            'status' => $this->status,
            'created' => $this->created,
            'approved_time' => $this->approved_time,
            'expire_date' => $this->expire_date,
        ]);

        $query->andFilterWhere(['like', 'full_name', $this->full_name])
            ->andFilterWhere(['like', 'permanent_address', $this->permanent_address])
            ->andFilterWhere(['like', 'nic_number', $this->nic_number])
            ->andFilterWhere(['like', 'purchase_place', $this->purchase_place])
            ->andFilterWhere(['like', 'export_countries', $this->export_countries])
            ->andFilterWhere(['like', 'contact_value', $this->contact_value])
            ->andFilterWhere(['like', 'approval_stage', $this->approval_stage]);

        return $dataProvider;
    }
}
