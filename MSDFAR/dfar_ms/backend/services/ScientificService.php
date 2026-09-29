<?php

namespace backend\services;

use backend\models\ScientificData;
use backend\models\ScientificFleetData;
use backend\models\ScientificSamplingBoatGearData;
use backend\models\ScientificSamplingCatchData;
use backend\models\ScientificSamplingData;
use backend\models\ScientificSamplingGearData;
use backend\models\ScientificSamplingLengthDetails;
use backend\models\ScientificSamplingOperationCost;
use backend\models\ScientificSamplingOperationUtilization;
use Yii;
use yii\web\BadRequestHttpException;

class ScientificService
{
    public function validateData($data)
    {
        if (!isset($data["fleet"]) || sizeof($data["fleet"])==0){
            echo json_encode(["success" => false, 'message' => "Please fill all fleet data"]);
            exit();
        }
        $samplingCraftSize = sizeof($data['craft']);

        if (sizeof($data['sampling']) < $samplingCraftSize || sizeof($data['craft']) < $samplingCraftSize || sizeof($data['lengthWeight']) < $samplingCraftSize) {
            echo json_encode(["success" => false, 'message' => "Please fill all data for your sampling craft"]);
            exit();
        }
    }

    /**
     * @throws BadRequestHttpException
     */
    public function saveScientificData($transaction, $data): int
    {
        $model = new ScientificData();
        $model->district = $data["fiDistrict"];
        $model->division = $data["fi_division"];
        $model->landing_site = $data["landing_place"];
        $model->added_by = Yii::$app->user->identity->id;
        $model->start_time = date("Y-m-d H:i:s",strtotime($data["sampling_date"])); // sampling date
        $model->end_time = date("Y-m-d H:i:s"); // submitted time
        $model->status = 1;
        $model->approval_stage = "Pending";

        if ($model->save()) {
            return $model->id;
        }

        $transaction->rollBack();
        Yii::error('saveScientificData: Error' . print_r($data, true), 'scientific');
        throw new BadRequestHttpException(Yii::t('app', 'Something went wrong when saving ScientificData'));



    }

    /**
     * @throws BadRequestHttpException
     */
    public function saveFleetData($transaction, $scientificId, $fleet)
    {
        foreach ($fleet as $item) {
            $model = new ScientificFleetData();
            $model->boat_type = $item['boat_type']["Id"];
            $model->gear_type = $item['gear_type']["Id"];
            $model->sub_category = $item['sub_category']["Id"];
            $model->no_of_boats = $item['number'];
            $model->scientific_id = $scientificId;
            if ($model->save()) {

            } else {
                $transaction->rollBack();
                Yii::error('saveFleetData: Error =>' . print_r($fleet, true), 'scientific');

                throw new BadRequestHttpException(Yii::t('app', 'Something went wrong when saving fleet data'));

            }

        }

    }

    /**
     * @throws BadRequestHttpException
     */
    public function saveSamplingCrafts($transaction, int $scientificId, $data)
    {
        $craft = $data["craft"];
        foreach ($craft as $key => $value) {
            $craftModel = new ScientificSamplingData();
            $craftModel->boat_number = $value["boatNumber"];
            $craftModel->status = 1;
            $craftModel->scientific_id = $scientificId;
            if ($craftModel->save()) {
                $this->saveBoatAndGearData($transaction, $craftModel->id, $data["sampling"][$key]);
                $this->saveCatchData($transaction, $craftModel->id, $data["catch"][$key]);
                $this->saveLengthWeight($transaction, $craftModel->id, $data["lengthWeight"][$key]);
                if (isset($data["operationCost"][$key]))
                    $this->saveOperationalCost($transaction, $craftModel->id, $data["operationCost"][$key]);

            } else {
                $transaction->rollBack();
                Yii::error('saveSamplingCrafts: Error =>' . print_r($craft, true), 'scientific');

                throw new BadRequestHttpException(Yii::t('app', 'Something went wrong when saving ScientificSamplingData'));

            }
        }
    }

    private function saveBoatAndGearData($transaction, int $craftId, $boatGearData)
    {
        $boatGearDataModel = new ScientificSamplingBoatGearData();
        $boatGearDataModel->sampling_data_id = $craftId;
        $boatGearDataModel->fishey_type = $boatGearData['fishery_type'];
        $boatGearDataModel->sub_category = $boatGearData['sub_cat2'];
        $boatGearDataModel->engine_hp = $boatGearData['hp'];
        $boatGearDataModel->departure_date = $boatGearData['dep_date'];
        $boatGearDataModel->departure_time = $boatGearData['dep_time'];
        $boatGearDataModel->departure_district = $boatGearData['dep_fi_district']["Id"];
        $boatGearDataModel->departure_division = $boatGearData['dep_fi_division']["Id"];
        $boatGearDataModel->depature_port = $boatGearData['dep_landing_place']['Id'];
        $boatGearDataModel->weather = implode(',', $boatGearData['weather']);
        $boatGearDataModel->arrival_date = $boatGearData['arrival_date'];
        $boatGearDataModel->remark = $boatGearData['remarks']??" ";
        $boatGearDataModel->unloading_type = $boatGearData['unloading-type'];
        $boatGearDataModel->gear_setting_time = $boatGearData['gear_set_time'];
        $boatGearDataModel->crew_members_count = $boatGearData['no_crew'];
        $boatGearDataModel->days = $boatGearData['days'];
        $boatGearDataModel->hours = $boatGearData['hours'];
        $boatGearDataModel->status = 1;

        if ($boatGearDataModel->save()) {
            $this->saveBoatGears($transaction, $boatGearDataModel->id, $boatGearData["gears"]);
        } else {
            $transaction->rollBack();
            Yii::error('saveBoatAndGearData: Error =>' . print_r($boatGearData, true), 'scientific');

            throw new BadRequestHttpException(Yii::t('app', 'Something went wrong when saving ScientificSamplingBoatGearData'));

        }

    }

    /**
     * @throws BadRequestHttpException
     */
    private function saveCatchData($transaction, int $id, $catchData)
    {
        foreach ($catchData as $key => $value) {

            if (isset($value["details"]) && $value["details"] != null)
                $this->saveCatchList($transaction, $id, str_replace("gear_used_", "", $key), $value["details"]);
        }

    }

    /**
     * @throws BadRequestHttpException
     */
    private function saveLengthWeight($transaction, int $id, $lengthData)
    {
        foreach ($lengthData as $key => $value) {

            if (isset($value["details"]) && $value["details"] != null)
                $this->saveLengthList($transaction, $id, str_replace("gear_used_", "", $key), $value["details"]);
        }

    }

    private function saveBoatGears($transaction, int $id, $gears)
    {
        $this->saveGearWithType($transaction, $id, $gears["mainGear"], "mainGear");
        $this->saveGearWithType($transaction, $id, $gears["secondGear"], "secondGear");
        $this->saveGearWithType($transaction, $id, $gears["thirdGear"], "thirdGear");
    }

    /**
     * @param int $id
     * @param $gear
     * @return void
     */
    public function saveGearWithType($transaction, int $id, $gear, $type)
    {
        if ($gear["gear"]["Id"] != "") {
            $model = new ScientificSamplingGearData();
            $model->boat_data_id = $id;
            $model->gear = $gear["gear"]["Id"];
            $model->target_species = $gear["target_species_main"]["Id"];
            $model->operations_per_trip = $gear["operatoin_no"];
            $model->fishing_time_days = $gear["days"];
            $model->fishing_time_hours = $gear["hours"];
            $model->fishing_depth = $gear["fishing_depth"];
            $model->g_code = $gear["g_code"];
            $model->extra = json_encode($gear["extra"] ?? "");
            $model->type = $type;
            $model->status = 1;
            if ($model->save()) {

            } else {
                $transaction->rollBack();
                Yii::error('saveGearWithType: Error =>' . print_r($gear, true), 'scientific');

                throw new BadRequestHttpException(Yii::t('app', 'Something went wrong when saving ScientificSamplingBoatGearData'));

            }


        }
    }

    /**
     * @throws BadRequestHttpException
     */
    private function saveCatchList($transaction, int $id, $gear, $details)
    {
        foreach ($details as $item) {
            $model = new ScientificSamplingCatchData();
            $model->craft_id = $id;
            $model->gear = $gear;
            $model->specie = $item['catch_species_code'];
            $model->weight_code = $item['catch_weight_code'];
            $model->weight = $item['catch_weight'];
            if ($model->save()) {

            } else {
                $transaction->rollBack();
                Yii::error('saveCatchList: Error =>' . print_r($details, true), 'scientific');

                throw new BadRequestHttpException(Yii::t('app', 'Something went wrong when saving ScientificSamplingCatchData'));

            }
        }

    }

    private function saveLengthList($transaction, int $id, $gear, $details)
    {
        foreach ($details as $item) {
            $model = new ScientificSamplingLengthDetails();
            $model->sampling_data_id = $id;
            $model->gear = $gear;
            $model->specie = $item['lw_species_code'];
            $model->specie_count = $item['lw_no_of_sample'] ?? 0;
            $model->weight_code = $item['lw_weight_code'];
            $model->weight = $item['lw_weight'];
            $model->length_type = $item['lw_length_type'];
            $model->length_code = $item['lw_length_code'];
            $model->length = $item['lw_length'];
            $model->status = 1;
            if ($model->save()) {

            } else {
                $transaction->rollBack();
                Yii::error('saveLengthList: Error =>' . print_r($details, true), 'scientific');
                $model->validate();
                throw new BadRequestHttpException(Yii::t('app', 'Something went wrong when saving ScientificSamplingLengthDetails' . print_r($model->getErrors(), true)));

            }
        }

    }

    /**
     * @throws BadRequestHttpException
     */
    private function saveOperationalCost($transaction, int $id, $operationCost)
    {
        $model = new ScientificSamplingOperationCost();
        $model->fuel_qty = $operationCost['fuel_qty'] ?? null;
        $model->fuel_price = $operationCost['fuel_val'] ?? null;
        $model->ice_qty = $operationCost['ice_qty'] ?? null;
        $model->ice_price = $operationCost['ice_val'] ?? null;
        $model->bait_qty = $operationCost['bait_qty'] ?? null;
        $model->bait_price = $operationCost['bait_val'] ?? null;
        $model->labour_cost = $operationCost['labour_cost'] ?? null;
        $model->food_water = $operationCost['food_water'] ?? null;
        $model->other = $operationCost['others'] ?? null;
        $model->remark = $operationCost['remarks'] ?? null;
        $model->sampling_data_id = $id;
        if ($model->save()) {

            if (isset($operationCost['catch']) && $operationCost['catch'] != null)
                $this->saveUtilization($transaction,$model->id, $operationCost['catch']);
        }else {
            $transaction->rollBack();
            Yii::error('saveOperationalCost: Error =>' . print_r($operationCost, true), 'scientific');

            throw new BadRequestHttpException(Yii::t('app', 'Something went wrong when saving ScientificSamplingOperationCost'));

        }
    }

    /**
     * @throws BadRequestHttpException
     */
    private function saveUtilization($transaction, int $id, $catch)
    {
        foreach ($catch as $item) {
            $model = new ScientificSamplingOperationUtilization();
            $model->operation_cost_id = $id;
            $model->specie = $item['species_code'] ?? null;
            $model->export_qty = $item['export_qty'] ?? null;
            $model->export_value = $item['export_val'] ?? null;
            $model->local_qty = $item['local_qty'] ?? null;
            $model->local_value = $item['local_val'] ?? null;
            $model->dried_qty = $item['dry_qty'] ?? null;
            $model->dried_value = $item['dry_val'] ?? null;
            $model->discard_qty = $item['discard_qty'] ?? null;
            $model->discard_value = $item['discard_val'] ?? null;
            $model->trash = $item['trash'] ?1:0 ;
            $model->status = 1;
            if ($model->save()) {

            } else {
                $transaction->rollBack();
                Yii::error('saveUtilization: Error =>' . print_r($catch, true), 'scientific');

                throw new BadRequestHttpException(Yii::t('app', 'Something went wrong when saving ScientificSamplingOperationUtilization'));

            }
        }
    }
}