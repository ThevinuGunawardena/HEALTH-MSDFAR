<?php

namespace backend\models;

use backend\config\Constant;
use backend\services\Util;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ApplicationexportbechedemerSearch represents the model behind the search form of `backend\models\Applicationexportbechedemer`.
 */
class ApplicationexportbechedemerSearch extends Applicationexportbechedemer
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name', 'address', 'telephone_number', 'email', 'business_reg_number', 'commercial_name', 'collection_area', 'export_country', 'additional_information', 'approval_stage', 'created', 'approved_time', 'expire_date'], 'safe'],
            [['fax_number', 'id', 'company', 'status'], 'integer'],
            [['tnc'], 'string'],
            [['charges'], 'number'],
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
        $query = Applicationexportbechedemer::find();

        // add conditions that should always apply here
        if (Yii::$app->user->identity->type == Constant::EXPORT_COMPANY && Util::editPermission()) {
            $query->where(["company" => Yii::$app->user->identity->profile_id]);
        }
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

        // grid filtering conditions
        $query->andFilterWhere([
            'fax_number' => $this->fax_number,
            'id' => $this->id,
            'company' => $this->company,

            'charges' => $this->charges,

            'status' => $this->status,
            'created' => $this->created,
            'approved_time' => $this->approved_time,
            'expire_date' => $this->expire_date,
        ]);

        $query->andFilterWhere(['like', 'full_name', $this->full_name])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'telephone_number', $this->telephone_number])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'business_reg_number', $this->business_reg_number])
            ->andFilterWhere(['like', 'export_country', $this->export_country])
            ->andFilterWhere(['like', 'additional_information', $this->additional_information])
            ->andFilterWhere(['like', 'export_countries', $this->export_countries])
            ->andFilterWhere(['like', 'approval_stage', $this->approval_stage]);

        return $dataProvider;
    }
}
