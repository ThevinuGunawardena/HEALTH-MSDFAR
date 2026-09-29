<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\MeaBoatRegistrationSearch $model */
/** @var yii\widgets\ActiveForm $form */

//
//$boatData = ArrayHelper::map(BoatNumbers::find()->andWhere(['id' => $model->boat_reg_number])->all(), 'id', function ($model) {
//    return $model['boat_number'];
//});
?>

<div class="mea-boat-registration-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index-mea?boat_number=' . Yii::$app->getRequest()->getQueryParam('boat_number')],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>


    <!--    --><?php //= $form->field($model, 'boat_number_id')->widget(Select2::classname(), [
    //        'data' => [],
    //
    //        'options' => ['placeholder' => 'Search...'],
    //        'pluginOptions' => [
    //            'allowClear' => false,
    //
    //            'minimumInputLength' => 3,
    //            'language' => [
    //                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
    //            ],
    //            'ajax' => [
    //                'url' => "../boat-numbers/search",
    //                'dataType' => 'json',
    //                'data' => new JsExpression('function(params) { return {q:params.term}; }')
    //            ],
    //            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
    //            'templateResult' => new JsExpression('function(boat_number) { return boat_number.text; }'),
    //            'templateSelection' => new JsExpression('function (boat_number) { return boat_number.text; }'),
    //        ],
    //    ]); ?>
    <?php // echo $form->field($model, 'date_of_build') ?>

    <?php // echo $form->field($model, 'material_of_hull_as_approved') ?>

    <?php // echo $form->field($model, 'gross_tonns') ?>

    <?php // echo $form->field($model, 'volume') ?>

    <?php // echo $form->field($model, 'overall_length') ?>

    <?php // echo $form->field($model, 'depth') ?>

    <?php // echo $form->field($model, 'beam') ?>

    <?php // echo $form->field($model, 'draught') ?>

    <?php // echo $form->field($model, 'engine_type') ?>

    <?php // echo $form->field($model, 'fuel_type') ?>

    <?php // echo $form->field($model, 'number_of_cylinders') ?>

    <?php // echo $form->field($model, 'propeller_diameter_pitch_no_of_blades') ?>

    <?php // echo $form->field($model, 'horse_power_of_engine') ?>

    <?php // echo $form->field($model, 'gear_ratio') ?>

    <?php // echo $form->field($model, 'engine_model') ?>

    <?php // echo $form->field($model, 'steering_gear_type') ?>

    <?php // echo $form->field($model, 'engine_number') ?>

    <?php // echo $form->field($model, 'engine_model_number') ?>

    <?php // echo $form->field($model, 'fuel_oil') ?>

    <?php // echo $form->field($model, 'fish_hold') ?>

    <?php // echo $form->field($model, 'fresh_water') ?>

    <?php // echo $form->field($model, 'stores') ?>

    <?php // echo $form->field($model, 'chilled_bath') ?>

    <?php // echo $form->field($model, 'designed_speed') ?>

    <?php // echo $form->field($model, 'place_of_inspectio') ?>
    <!--    --><?php // echo $form->field($model, 'mea_certificate_number') ?>
    <!--    --><?php // echo $form->field($model, 'inspection_date')->textInput(['type'=>"date"]) ?>

    <?php // echo $form->field($model, 'vessel_at_the_time_of_inspection') ?>

    <?php // echo $form->field($model, 'hull_hull_framing') ?>

    <?php // echo $form->field($model, 'hatch_way_coamings') ?>

    <?php // echo $form->field($model, 'inlets_discharges') ?>

    <?php // echo $form->field($model, 'machinery_space_opening') ?>

    <?php // echo $form->field($model, 'deck_deck_framing') ?>

    <?php // echo $form->field($model, 'deck_opening') ?>

    <?php // echo $form->field($model, 'bulk_head') ?>

    <?php // echo $form->field($model, 'pipes_bunkering_inlets') ?>

    <?php // echo $form->field($model, 'weather_tight_doors') ?>

    <?php // echo $form->field($model, 'freeing_ports') ?>

    <?php // echo $form->field($model, 'fishing_gear_symbol') ?>

    <?php // echo $form->field($model, 'bow_height') ?>

    <?php // echo $form->field($model, 'chilled_bath_partitions') ?>

    <?php // echo $form->field($model, 'maximum_draught') ?>

    <?php // echo $form->field($model, 'bait_hold_arrangement') ?>

    <?php // echo $form->field($model, 'water_tanks_partitions') ?>

    <?php // echo $form->field($model, 'stores_cargo_hold_constructions') ?>

    <?php // echo $form->field($model, 'fuel_tanks_partitions') ?>

    <?php // echo $form->field($model, 'draught_marks') ?>

    <?php // echo $form->field($model, 'portable_fish_hold_divisions') ?>

    <?php // echo $form->field($model, 'stability_notice') ?>

    <?php // echo $form->field($model, 'layout_of_machinery_space') ?>

    <?php // echo $form->field($model, 'console_monitoring_instruments') ?>

    <?php // echo $form->field($model, 'propulsion_machinery_steering_gear') ?>

    <?php // echo $form->field($model, 'fuel_oil_installation') ?>

    <?php // echo $form->field($model, 'engine_console') ?>

    <?php // echo $form->field($model, 'cooling_water_system') ?>

    <?php // echo $form->field($model, 'lighting') ?>

    <?php // echo $form->field($model, 'bilge_pumping_systems') ?>

    <?php // echo $form->field($model, 'floor') ?>

    <?php // echo $form->field($model, 'exhaust_systems') ?>

    <?php // echo $form->field($model, 'ventilation') ?>

    <?php // echo $form->field($model, 'hydraulic_system') ?>

    <?php // echo $form->field($model, 'sound_vibration') ?>

    <?php // echo $form->field($model, 'refrigeration_system') ?>

    <?php // echo $form->field($model, 'engine_mounting') ?>

    <?php // echo $form->field($model, 'main_source_electrical_supply') ?>

    <?php // echo $form->field($model, 'earthing_bonding') ?>

    <?php // echo $form->field($model, 'electrical_system') ?>

    <?php // echo $form->field($model, 'lighting_system') ?>

    <?php // echo $form->field($model, 'direct_current_system') ?>

    <?php // echo $form->field($model, 'electric_motors') ?>

    <?php // echo $form->field($model, 'alternating_current_system') ?>

    <?php // echo $form->field($model, 'conductors_nodes_breakers') ?>

    <?php // echo $form->field($model, 'storage_of_gas_cylinders') ?>

    <?php // echo $form->field($model, 'means_of_escape') ?>

    <?php // echo $form->field($model, 'firefighting_appliances') ?>

    <?php // echo $form->field($model, 'fire_hydrants_fire_horses_nozzles') ?>

    <?php // echo $form->field($model, 'ventilation_system') ?>

    <?php // echo $form->field($model, 'surfaces_of_deck') ?>

    <?php // echo $form->field($model, 'medical_facilities') ?>

    <?php // echo $form->field($model, 'deck_opening_doors') ?>

    <?php // echo $form->field($model, 'dangerous_areas') ?>

    <?php // echo $form->field($model, 'bulwark_rails_guards') ?>

    <?php // echo $form->field($model, 'life_jackets_personal_flotation_devices') ?>

    <?php // echo $form->field($model, 'stairways_ladders') ?>

    <?php // echo $form->field($model, 'lifebuoys') ?>

    <?php // echo $form->field($model, 'cooking_facilities') ?>

    <?php // echo $form->field($model, 'distress_signals') ?>

    <?php // echo $form->field($model, 'deck_machinery_tackles_lifting_gear') ?>

    <?php // echo $form->field($model, 'source_of_energy') ?>

    <?php // echo $form->field($model, 'navigation_lights') ?>

    <?php // echo $form->field($model, 'radio_installation_equipment') ?>

    <?php // echo $form->field($model, 'crew_accommodation') ?>

    <?php // echo $form->field($model, 'magnetic_compass') ?>

    <?php // echo $form->field($model, 'lighting_heating_ventilating') ?>

    <?php // echo $form->field($model, 'gps_satellite_navigation_system') ?>

    <?php // echo $form->field($model, 'sleeping_spaces') ?>

    <?php // echo $form->field($model, 'means_depth_finding') ?>

    <?php // echo $form->field($model, 'eating_spaces_cooking_facilities') ?>

    <?php // echo $form->field($model, 'nautical_instruments_publications') ?>

    <?php // echo $form->field($model, 'sanitary_facilities') ?>

    <?php // echo $form->field($model, 'signaling_system') ?>

    <?php // echo $form->field($model, 'water_facilities') ?>

    <?php // echo $form->field($model, 'navigation_bridge_visibility') ?>

    <?php // echo $form->field($model, 'secondary_means_of_starting') ?>

    <?php // echo $form->field($model, 'emergency_steering_arrangement') ?>

    <?php // echo $form->field($model, 'emergency_source_of_electrical_power') ?>

    <?php // echo $form->field($model, 'essential_machinery_spares_and_tools_required') ?>

    <?php // echo $form->field($model, 'call_sign_number') ?>

    <?php // echo $form->field($model, 'vms_number') ?>

    <?php // echo $form->field($model, 'date_of_repairs_approved_modifications_hull') ?>

    <?php // echo $form->field($model, 'date_of_repairs_approved_modifications_machinery') ?>

    <?php // echo $form->field($model, 'running_trail_carried_out_on') ?>

    <?php // echo $form->field($model, 'machinery_operation') ?>

    <?php // echo $form->field($model, 'maneuverability') ?>

    <?php // echo $form->field($model, 'declaration') ?>

    <!--    --><?php // echo $form->field($model, 'next_inspected_date')->textInput(['type'=>"date"]) ?>



    <?php // echo $form->field($model, 'number_of_professional_competent_crew') ?>

    <?php // echo $form->field($model, 'name_of_coxswain_certificate_no') ?>

    <?php // echo $form->field($model, 'name_of_engine_driver_certificate_no') ?>

    <?php // echo $form->field($model, 'other_information') ?>

    <?php // echo $form->field($model, 'value_of_boat_hull') ?>

    <?php // echo $form->field($model, 'value_of_boat_engine') ?>

    <?php // echo $form->field($model, 'compass') ?>

    <?php // echo $form->field($model, 'ais') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'created') ?>

    <?php // echo $form->field($model, 'status') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
