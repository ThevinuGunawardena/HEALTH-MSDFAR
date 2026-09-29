<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\MeaBoatRegistration;

/**
 * MeaBoatRegistrationSearch represents the model behind the search form of `backend\models\MeaBoatRegistration`.
 */
class MeaBoatRegistrationSearch extends MeaBoatRegistration
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'boat_reg_number', 'vessel_type', 'date_of_build', 'engine_type', 'fuel_type', 'number_of_cylinders', 'horse_power_of_engine', 'engine_model', 'vessel_at_the_time_of_inspection', 'hull_hull_framing', 'hatch_way_coamings', 'inlets_discharges', 'machinery_space_opening', 'deck_deck_framing', 'deck_opening', 'bulk_head', 'pipes_bunkering_inlets', 'weather_tight_doors', 'freeing_ports', 'fishing_gear_symbol', 'bow_height', 'chilled_bath_partitions', 'maximum_draught', 'bait_hold_arrangement', 'water_tanks_partitions', 'stores_cargo_hold_constructions', 'fuel_tanks_partitions', 'draught_marks', 'portable_fish_hold_divisions', 'stability_notice', 'layout_of_machinery_space', 'console_monitoring_instruments', 'propulsion_machinery_steering_gear', 'fuel_oil_installation', 'engine_console', 'cooling_water_system', 'lighting', 'bilge_pumping_systems', 'floor', 'exhaust_systems', 'ventilation', 'hydraulic_system', 'sound_vibration', 'refrigeration_system', 'engine_mounting', 'main_source_electrical_supply', 'earthing_bonding', 'electrical_system', 'lighting_system', 'direct_current_system', 'electric_motors', 'alternating_current_system', 'conductors_nodes_breakers', 'storage_of_gas_cylinders', 'means_of_escape', 'firefighting_appliances', 'fire_hydrants_fire_horses_nozzles', 'ventilation_system', 'surfaces_of_deck', 'medical_facilities', 'deck_opening_doors', 'dangerous_areas', 'bulwark_rails_guards', 'life_jackets_personal_flotation_devices', 'stairways_ladders', 'lifebuoys', 'cooking_facilities', 'distress_signals', 'deck_machinery_tackles_lifting_gear', 'source_of_energy', 'navigation_lights', 'radio_installation_equipment', 'crew_accommodation', 'magnetic_compass', 'lighting_heating_ventilating', 'gps_satellite_navigation_system', 'sleeping_spaces', 'means_depth_finding', 'eating_spaces_cooking_facilities', 'nautical_instruments_publications', 'sanitary_facilities', 'signaling_system', 'water_facilities', 'navigation_bridge_visibility', 'secondary_means_of_starting', 'emergency_steering_arrangement', 'emergency_source_of_electrical_power', 'machinery_operation', 'maneuverability', 'declaration', 'number_of_professional_competent_crew', 'ais', 'created_by', 'status'], 'integer'],
            [['hull_number', 'light_weight_type_of_vessel', 'commenced_construction_date', 'completed_construction_date', 'material_of_hull_as_approved', 'propeller_diameter_pitch_no_of_blades', 'gear_ratio', 'steering_gear_type', 'engine_number', 'engine_model_number', 'designed_speed', 'place_of_inspectio', 'inspection_date', 'essential_machinery_spares_and_tools_required', 'call_sign_number', 'vms_number', 'date_of_repairs_approved_modifications_hull', 'date_of_repairs_approved_modifications_machinery', 'running_trail_carried_out_on', 'next_inspected_date', 'mea_certificate_number', 'name_of_coxswain_certificate_no', 'name_of_engine_driver_certificate_no', 'other_information', 'compass', 'created'], 'safe'],
            [['gross_tonns', 'volume', 'overall_length', 'depth', 'beam', 'draught', 'fuel_oil', 'fish_hold', 'fresh_water', 'stores', 'chilled_bath', 'value_of_boat_hull', 'value_of_boat_engine'], 'number'],
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
    public function search($params,$boat_number)
    {
        $query = MeaBoatRegistration::find();

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
        $query->where(["boat_reg_number"=>$boat_number]);
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'boat_reg_number' => $this->boat_reg_number,
            'vessel_type' => $this->vessel_type,
            'commenced_construction_date' => $this->commenced_construction_date,
            'completed_construction_date' => $this->completed_construction_date,
            'date_of_build' => $this->date_of_build,
            'gross_tonns' => $this->gross_tonns,
            'volume' => $this->volume,
            'overall_length' => $this->overall_length,
            'depth' => $this->depth,
            'beam' => $this->beam,
            'draught' => $this->draught,
            'engine_type' => $this->engine_type,
            'fuel_type' => $this->fuel_type,
            'number_of_cylinders' => $this->number_of_cylinders,
            'horse_power_of_engine' => $this->horse_power_of_engine,
            'engine_model' => $this->engine_model,
            'fuel_oil' => $this->fuel_oil,
            'fish_hold' => $this->fish_hold,
            'fresh_water' => $this->fresh_water,
            'stores' => $this->stores,
            'chilled_bath' => $this->chilled_bath,
            'inspection_date' => $this->inspection_date,
            'vessel_at_the_time_of_inspection' => $this->vessel_at_the_time_of_inspection,
            'hull_hull_framing' => $this->hull_hull_framing,
            'hatch_way_coamings' => $this->hatch_way_coamings,
            'inlets_discharges' => $this->inlets_discharges,
            'machinery_space_opening' => $this->machinery_space_opening,
            'deck_deck_framing' => $this->deck_deck_framing,
            'deck_opening' => $this->deck_opening,
            'bulk_head' => $this->bulk_head,
            'pipes_bunkering_inlets' => $this->pipes_bunkering_inlets,
            'weather_tight_doors' => $this->weather_tight_doors,
            'freeing_ports' => $this->freeing_ports,
            'fishing_gear_symbol' => $this->fishing_gear_symbol,
            'bow_height' => $this->bow_height,
            'chilled_bath_partitions' => $this->chilled_bath_partitions,
            'maximum_draught' => $this->maximum_draught,
            'bait_hold_arrangement' => $this->bait_hold_arrangement,
            'water_tanks_partitions' => $this->water_tanks_partitions,
            'stores_cargo_hold_constructions' => $this->stores_cargo_hold_constructions,
            'fuel_tanks_partitions' => $this->fuel_tanks_partitions,
            'draught_marks' => $this->draught_marks,
            'portable_fish_hold_divisions' => $this->portable_fish_hold_divisions,
            'stability_notice' => $this->stability_notice,
            'layout_of_machinery_space' => $this->layout_of_machinery_space,
            'console_monitoring_instruments' => $this->console_monitoring_instruments,
            'propulsion_machinery_steering_gear' => $this->propulsion_machinery_steering_gear,
            'fuel_oil_installation' => $this->fuel_oil_installation,
            'engine_console' => $this->engine_console,
            'cooling_water_system' => $this->cooling_water_system,
            'lighting' => $this->lighting,
            'bilge_pumping_systems' => $this->bilge_pumping_systems,
            'floor' => $this->floor,
            'exhaust_systems' => $this->exhaust_systems,
            'ventilation' => $this->ventilation,
            'hydraulic_system' => $this->hydraulic_system,
            'sound_vibration' => $this->sound_vibration,
            'refrigeration_system' => $this->refrigeration_system,
            'engine_mounting' => $this->engine_mounting,
            'main_source_electrical_supply' => $this->main_source_electrical_supply,
            'earthing_bonding' => $this->earthing_bonding,
            'electrical_system' => $this->electrical_system,
            'lighting_system' => $this->lighting_system,
            'direct_current_system' => $this->direct_current_system,
            'electric_motors' => $this->electric_motors,
            'alternating_current_system' => $this->alternating_current_system,
            'conductors_nodes_breakers' => $this->conductors_nodes_breakers,
            'storage_of_gas_cylinders' => $this->storage_of_gas_cylinders,
            'means_of_escape' => $this->means_of_escape,
            'firefighting_appliances' => $this->firefighting_appliances,
            'fire_hydrants_fire_horses_nozzles' => $this->fire_hydrants_fire_horses_nozzles,
            'ventilation_system' => $this->ventilation_system,
            'surfaces_of_deck' => $this->surfaces_of_deck,
            'medical_facilities' => $this->medical_facilities,
            'deck_opening_doors' => $this->deck_opening_doors,
            'dangerous_areas' => $this->dangerous_areas,
            'bulwark_rails_guards' => $this->bulwark_rails_guards,
            'life_jackets_personal_flotation_devices' => $this->life_jackets_personal_flotation_devices,
            'stairways_ladders' => $this->stairways_ladders,
            'lifebuoys' => $this->lifebuoys,
            'cooking_facilities' => $this->cooking_facilities,
            'distress_signals' => $this->distress_signals,
            'deck_machinery_tackles_lifting_gear' => $this->deck_machinery_tackles_lifting_gear,
            'source_of_energy' => $this->source_of_energy,
            'navigation_lights' => $this->navigation_lights,
            'radio_installation_equipment' => $this->radio_installation_equipment,
            'crew_accommodation' => $this->crew_accommodation,
            'magnetic_compass' => $this->magnetic_compass,
            'lighting_heating_ventilating' => $this->lighting_heating_ventilating,
            'gps_satellite_navigation_system' => $this->gps_satellite_navigation_system,
            'sleeping_spaces' => $this->sleeping_spaces,
            'means_depth_finding' => $this->means_depth_finding,
            'eating_spaces_cooking_facilities' => $this->eating_spaces_cooking_facilities,
            'nautical_instruments_publications' => $this->nautical_instruments_publications,
            'sanitary_facilities' => $this->sanitary_facilities,
            'signaling_system' => $this->signaling_system,
            'water_facilities' => $this->water_facilities,
            'navigation_bridge_visibility' => $this->navigation_bridge_visibility,
            'secondary_means_of_starting' => $this->secondary_means_of_starting,
            'emergency_steering_arrangement' => $this->emergency_steering_arrangement,
            'emergency_source_of_electrical_power' => $this->emergency_source_of_electrical_power,
            'date_of_repairs_approved_modifications_hull' => $this->date_of_repairs_approved_modifications_hull,
            'date_of_repairs_approved_modifications_machinery' => $this->date_of_repairs_approved_modifications_machinery,
            'running_trail_carried_out_on' => $this->running_trail_carried_out_on,
            'machinery_operation' => $this->machinery_operation,
            'maneuverability' => $this->maneuverability,
            'declaration' => $this->declaration,
            'next_inspected_date' => $this->next_inspected_date,
            'number_of_professional_competent_crew' => $this->number_of_professional_competent_crew,
            'value_of_boat_hull' => $this->value_of_boat_hull,
            'value_of_boat_engine' => $this->value_of_boat_engine,
            'ais' => $this->ais,
            'created_by' => $this->created_by,
            'created' => $this->created,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'hull_number', $this->hull_number])
            ->andFilterWhere(['like', 'light_weight_type_of_vessel', $this->light_weight_type_of_vessel])
            ->andFilterWhere(['like', 'material_of_hull_as_approved', $this->material_of_hull_as_approved])
            ->andFilterWhere(['like', 'propeller_diameter_pitch_no_of_blades', $this->propeller_diameter_pitch_no_of_blades])
            ->andFilterWhere(['like', 'gear_ratio', $this->gear_ratio])
            ->andFilterWhere(['like', 'steering_gear_type', $this->steering_gear_type])
            ->andFilterWhere(['like', 'engine_number', $this->engine_number])
            ->andFilterWhere(['like', 'engine_model_number', $this->engine_model_number])
            ->andFilterWhere(['like', 'designed_speed', $this->designed_speed])
            ->andFilterWhere(['like', 'place_of_inspectio', $this->place_of_inspectio])
            ->andFilterWhere(['like', 'essential_machinery_spares_and_tools_required', $this->essential_machinery_spares_and_tools_required])
            ->andFilterWhere(['like', 'call_sign_number', $this->call_sign_number])
            ->andFilterWhere(['like', 'vms_number', $this->vms_number])
            ->andFilterWhere(['like', 'mea_certificate_number', $this->mea_certificate_number])
            ->andFilterWhere(['like', 'name_of_coxswain_certificate_no', $this->name_of_coxswain_certificate_no])
            ->andFilterWhere(['like', 'name_of_engine_driver_certificate_no', $this->name_of_engine_driver_certificate_no])
            ->andFilterWhere(['like', 'other_information', $this->other_information])
            ->andFilterWhere(['like', 'compass', $this->compass]);

        return $dataProvider;
    }
}
