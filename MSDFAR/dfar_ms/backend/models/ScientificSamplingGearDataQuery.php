<?php

namespace backend\models;

/**
 * This is the ActiveQuery class for [[ScientificSamplingGearData]].
 *
 * @see ScientificSamplingGearData
 */
class ScientificSamplingGearDataQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ScientificSamplingGearData[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ScientificSamplingGearData|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
