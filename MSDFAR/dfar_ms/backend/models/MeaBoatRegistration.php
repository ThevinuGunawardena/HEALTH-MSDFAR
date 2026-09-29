<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "mea_boat_registration".
 *
 * @property int $id
 * @property int $boat_reg_number
 * @property string $hull_number
 * @property int $vessel_type
 * @property int|null $reg_fishing_gear
 * @property string $light_weight_type_of_vessel
 * @property string $commenced_construction_date
 * @property string $completed_construction_date
 * @property string $date_of_build
 * @property string $material_of_hull_as_approved
 * @property float $gross_tonns
 * @property float $volume
 * @property float $overall_length
 * @property float $depth
 * @property float $beam
 * @property float $draught
 * @property int $engine_type
 * @property string $fuel_type
 * @property int|null $number_of_cylinders
 * @property string $propeller_diameter_pitch_no_of_blades
 * @property int $horse_power_of_engine
 * @property string $gear_ratio
 * @property int $engine_model
 * @property string $steering_gear_type
 * @property string $engine_number
 * @property string $engine_model_number
 * @property float $fuel_oil
 * @property float $fish_hold
 * @property float $fresh_water
 * @property string $stores
 * @property float|null $chilled_bath
 * @property string $designed_speed
 * @property string $place_of_inspectio
 * @property string $inspection_date
 * @property int $vessel_at_the_time_of_inspection
 * @property int $hull_hull_framing
 * @property int $hatch_way_coamings
 * @property int $inlets_discharges
 * @property int $machinery_space_opening
 * @property int $deck_deck_framing
 * @property int $deck_opening
 * @property int $bulk_head
 * @property int $pipes_bunkering_inlets
 * @property int $weather_tight_doors
 * @property int $freeing_ports
 * @property int $fishing_gear_symbol
 * @property int $bow_height
 * @property int $chilled_bath_partitions
 * @property int $maximum_draught
 * @property int $bait_hold_arrangement
 * @property int $water_tanks_partitions
 * @property int $stores_cargo_hold_constructions
 * @property int $fuel_tanks_partitions
 * @property int $draught_marks
 * @property int $portable_fish_hold_divisions
 * @property int $stability_notice
 * @property int $layout_of_machinery_space
 * @property int $console_monitoring_instruments
 * @property int $propulsion_machinery_steering_gear
 * @property int $fuel_oil_installation
 * @property int $gear_marking
 * @property int $dehooker_line_cutter
 * @property int $engine_console
 * @property int $cooling_water_system
 * @property int $lighting
 * @property int $bilge_pumping_systems
 * @property int $floor
 * @property int $exhaust_systems
 * @property int $ventilation
 * @property int $hydraulic_system
 * @property int $sound_vibration
 * @property int $refrigeration_system
 * @property int $engine_mounting
 * @property int $main_source_electrical_supply
 * @property int $earthing_bonding
 * @property int $electrical_system
 * @property int $lighting_system
 * @property int $direct_current_system
 * @property int $electric_motors
 * @property int $alternating_current_system
 * @property int $conductors_nodes_breakers
 * @property int $storage_of_gas_cylinders
 * @property int $means_of_escape
 * @property int $firefighting_appliances
 * @property int $fire_hydrants_fire_horses_nozzles
 * @property int $ventilation_system
 * @property int $surfaces_of_deck
 * @property int $medical_facilities
 * @property int $deck_opening_doors
 * @property int $dangerous_areas
 * @property int $bulwark_rails_guards
 * @property int $life_jackets_personal_flotation_devices
 * @property int $stairways_ladders
 * @property int $lifebuoys
 * @property int $cooking_facilities
 * @property int $distress_signals
 * @property int $deck_machinery_tackles_lifting_gear
 * @property int $source_of_energy
 * @property int $navigation_lights
 * @property int $radio_installation_equipment
 * @property int $crew_accommodation
 * @property int $magnetic_compass
 * @property int $lighting_heating_ventilating
 * @property int $gps_satellite_navigation_system
 * @property int $sleeping_spaces
 * @property int $means_depth_finding
 * @property int $eating_spaces_cooking_facilities
 * @property int $nautical_instruments_publications
 * @property int $sanitary_facilities
 * @property int $signaling_system
 * @property int $water_facilities
 * @property int $navigation_bridge_visibility
 * @property int $secondary_means_of_starting
 * @property int $emergency_steering_arrangement
 * @property int $emergency_source_of_electrical_power
 * @property string $essential_machinery_spares_and_tools_required
 * @property string $call_sign_number
 * @property string|null $vms_number
 * @property string|null $date_of_repairs_approved_modifications_hull
 * @property string|null $date_of_repairs_approved_modifications_machinery
 * @property string|null $running_trail_carried_out_on
 * @property int $machinery_operation
 * @property int $maneuverability
 * @property int $declaration
 * @property string $next_inspected_date
 * @property string $mea_certificate_number
 * @property int|null $condition_hull_internal
 * @property int|null $condition_hull_external
 * @property int|null $condition_hull_sheathing
 * @property int|null $condition_decks
 * @property int|null $condition_steering_gear
 * @property int|null $condition_cargo_compartment
 * @property int|null $condition_anchor_cables
 * @property int|null $condition_framework_timbers_internals
 * @property int|null $condition_navigation_lights
 * @property int|null $condition_machinery
 * @property int|null $condition_rudder
 * @property string|null $condition_date_last_overhaul
 * @property int|null $equipped_compass
 * @property int|null $equipped_bailers
 * @property int|null $equipped_life_saving_appliances
 * @property int|null $equipped_first_aid_equipment
 * @property int|null $equipped_fire_extingulshers_type
 * @property int|null $equipped_navigation_equipment
 * @property int|null $equipped_bilge_pump
 * @property int|null $equipped_gps_available
 * @property int|null $equipped_vms_installation
 * @property int|null $equipped_emergency_repairs
 * @property int $number_of_professional_competent_crew
 * @property int $number_of_professional_competent_crew_minimum
 * @property string $name_of_coxswain_certificate_no
 * @property string $name_of_engine_driver_certificate_no
 * @property string|null $other_information
 * @property float|null $value_of_boat_hull
 * @property float|null $value_of_boat_engine
 * @property string|null $compass
 * @property int|null $radio
 * @property int|null $radar
 * @property int|null $ais
 * @property int|null $winch
 * @property int|null $gps
 * @property int|null $vms
 * @property string|null $vms_installation
 * @property int|null $gillnets
 * @property int|null $longlines
 * @property int|null $other
 * @property int|null $total
 * @property int $ais_buoys
 * @property int $created_by
 * @property string $created
 * @property int $status
 * @property string $approval_stage
 * @property string|null $approved_time
 * @property string|null $expire_date
 * @property int $renew
 */
class MeaBoatRegistration extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mea_boat_registration';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['boat_reg_number', 'hull_number', 'vessel_type', 'light_weight_type_of_vessel', 'commenced_construction_date', 'completed_construction_date', 'date_of_build', 'material_of_hull_as_approved', 'gross_tonns', 'volume', 'overall_length', 'depth', 'beam', 'draught', 'engine_type', 'fuel_type', 'propeller_diameter_pitch_no_of_blades', 'horse_power_of_engine', 'gear_ratio', 'engine_model', 'steering_gear_type', 'engine_number', 'fuel_oil', 'fish_hold', 'fresh_water', 'stores', 'designed_speed', 'place_of_inspectio', 'inspection_date', 'vessel_at_the_time_of_inspection', 'hull_hull_framing', 'hatch_way_coamings', 'inlets_discharges', 'machinery_space_opening', 'deck_deck_framing', 'deck_opening', 'bulk_head', 'pipes_bunkering_inlets', 'weather_tight_doors', 'freeing_ports', 'fishing_gear_symbol', 'bow_height', 'chilled_bath_partitions', 'maximum_draught', 'bait_hold_arrangement', 'water_tanks_partitions', 'stores_cargo_hold_constructions', 'fuel_tanks_partitions', 'draught_marks', 'portable_fish_hold_divisions', 'stability_notice', 'layout_of_machinery_space', 'console_monitoring_instruments', 'propulsion_machinery_steering_gear', 'fuel_oil_installation', 'gear_marking', 'dehooker_line_cutter', 'engine_console', 'cooling_water_system', 'lighting', 'bilge_pumping_systems', 'floor', 'exhaust_systems', 'ventilation', 'hydraulic_system', 'sound_vibration', 'refrigeration_system', 'engine_mounting', 'main_source_electrical_supply', 'earthing_bonding', 'electrical_system', 'lighting_system', 'direct_current_system', 'electric_motors', 'alternating_current_system', 'conductors_nodes_breakers', 'storage_of_gas_cylinders', 'means_of_escape', 'firefighting_appliances', 'fire_hydrants_fire_horses_nozzles', 'ventilation_system', 'surfaces_of_deck', 'medical_facilities', 'deck_opening_doors', 'dangerous_areas', 'bulwark_rails_guards', 'life_jackets_personal_flotation_devices', 'stairways_ladders', 'lifebuoys', 'cooking_facilities', 'distress_signals', 'deck_machinery_tackles_lifting_gear', 'source_of_energy', 'navigation_lights', 'radio_installation_equipment', 'crew_accommodation', 'magnetic_compass', 'lighting_heating_ventilating', 'gps_satellite_navigation_system', 'sleeping_spaces', 'means_depth_finding', 'eating_spaces_cooking_facilities', 'nautical_instruments_publications', 'sanitary_facilities', 'signaling_system', 'water_facilities', 'navigation_bridge_visibility', 'secondary_means_of_starting', 'emergency_steering_arrangement', 'emergency_source_of_electrical_power', 'essential_machinery_spares_and_tools_required', 'call_sign_number', 'machinery_operation', 'maneuverability', 'declaration', 'next_inspected_date', 'mea_certificate_number', 'number_of_professional_competent_crew', 'number_of_professional_competent_crew_minimum', 'name_of_coxswain_certificate_no', 'name_of_engine_driver_certificate_no', 'ais_buoys', 'created_by', 'created', 'approval_stage', 'renew'], 'required'],
            [['boat_reg_number', 'vessel_type', 'reg_fishing_gear', 'engine_type', 'number_of_cylinders', 'horse_power_of_engine', 'engine_model', 'vessel_at_the_time_of_inspection', 'hull_hull_framing', 'hatch_way_coamings', 'inlets_discharges', 'machinery_space_opening', 'deck_deck_framing', 'deck_opening', 'bulk_head', 'pipes_bunkering_inlets', 'weather_tight_doors', 'freeing_ports', 'fishing_gear_symbol', 'bow_height', 'chilled_bath_partitions', 'maximum_draught', 'bait_hold_arrangement', 'water_tanks_partitions', 'stores_cargo_hold_constructions', 'fuel_tanks_partitions', 'draught_marks', 'portable_fish_hold_divisions', 'stability_notice', 'layout_of_machinery_space', 'console_monitoring_instruments', 'propulsion_machinery_steering_gear', 'fuel_oil_installation', 'gear_marking', 'dehooker_line_cutter', 'engine_console', 'cooling_water_system', 'lighting', 'bilge_pumping_systems', 'floor', 'exhaust_systems', 'ventilation', 'hydraulic_system', 'sound_vibration', 'refrigeration_system', 'engine_mounting', 'main_source_electrical_supply', 'earthing_bonding', 'electrical_system', 'lighting_system', 'direct_current_system', 'electric_motors', 'alternating_current_system', 'conductors_nodes_breakers', 'storage_of_gas_cylinders', 'means_of_escape', 'firefighting_appliances', 'fire_hydrants_fire_horses_nozzles', 'ventilation_system', 'surfaces_of_deck', 'medical_facilities', 'deck_opening_doors', 'dangerous_areas', 'bulwark_rails_guards', 'life_jackets_personal_flotation_devices', 'stairways_ladders', 'lifebuoys', 'cooking_facilities', 'distress_signals', 'deck_machinery_tackles_lifting_gear', 'source_of_energy', 'navigation_lights', 'radio_installation_equipment', 'crew_accommodation', 'magnetic_compass', 'lighting_heating_ventilating', 'gps_satellite_navigation_system', 'sleeping_spaces', 'means_depth_finding', 'eating_spaces_cooking_facilities', 'nautical_instruments_publications', 'sanitary_facilities', 'signaling_system', 'water_facilities', 'navigation_bridge_visibility', 'secondary_means_of_starting', 'emergency_steering_arrangement', 'emergency_source_of_electrical_power', 'machinery_operation', 'maneuverability', 'declaration', 'condition_hull_internal', 'condition_hull_external', 'condition_hull_sheathing', 'condition_decks', 'condition_steering_gear', 'condition_cargo_compartment', 'condition_anchor_cables', 'condition_framework_timbers_internals', 'condition_navigation_lights', 'condition_machinery', 'condition_rudder', 'equipped_compass', 'equipped_bailers', 'equipped_life_saving_appliances', 'equipped_first_aid_equipment', 'equipped_fire_extingulshers_type', 'equipped_navigation_equipment', 'equipped_bilge_pump', 'equipped_gps_available', 'equipped_vms_installation', 'equipped_emergency_repairs', 'number_of_professional_competent_crew', 'number_of_professional_competent_crew_minimum', 'radio', 'radar', 'ais', 'winch', 'gps', 'vms', 'gillnets', 'longlines', 'other', 'total', 'ais_buoys', 'created_by', 'status', 'renew'], 'integer'],
            [['commenced_construction_date', 'completed_construction_date', 'date_of_build', 'inspection_date', 'date_of_repairs_approved_modifications_hull', 'date_of_repairs_approved_modifications_machinery', 'running_trail_carried_out_on', 'next_inspected_date', 'condition_date_last_overhaul', 'created', 'approved_time', 'expire_date'], 'safe'],
            [['gross_tonns', 'volume', 'overall_length', 'depth', 'beam', 'draught', 'fuel_oil', 'fish_hold', 'fresh_water', 'chilled_bath', 'value_of_boat_hull', 'value_of_boat_engine'], 'number'],
            [['hull_number', 'gear_ratio', 'engine_number', 'engine_model_number', 'stores', 'designed_speed', 'place_of_inspectio', 'call_sign_number', 'vms_number', 'mea_certificate_number', 'vms_installation'], 'string', 'max' => 100],
            [['light_weight_type_of_vessel', 'material_of_hull_as_approved', 'steering_gear_type', 'essential_machinery_spares_and_tools_required', 'name_of_coxswain_certificate_no', 'name_of_engine_driver_certificate_no', 'compass', 'approval_stage'], 'string', 'max' => 200],
            [['fuel_type'], 'string', 'max' => 50],
            [['propeller_diameter_pitch_no_of_blades', 'other_information'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'boat_reg_number' => Yii::t('app', 'Boat Reg Number'),
            'hull_number' => Yii::t('app', 'Hull Number'),
            'vessel_type' => Yii::t('app', 'Vessel Type'),
            'reg_fishing_gear' => Yii::t('app', 'Reg Fishing Gear'),
            'light_weight_type_of_vessel' => Yii::t('app', 'Light Weight Type Of Vessel'),
            'commenced_construction_date' => Yii::t('app', 'Construction Commenced Date'),
            'completed_construction_date' => Yii::t('app', 'Construction Completed Date'),
            'date_of_build' => Yii::t('app', 'Date Of Build'),
            'material_of_hull_as_approved' => Yii::t('app', 'Material Of Hull As Approved'),
            'gross_tonns' => Yii::t('app', 'Gross Tonns (T)'),
            'volume' => Yii::t('app', 'Volume (m³)'),
            'overall_length' => Yii::t('app', 'Overall Length (meter)'),
            'depth' => Yii::t('app', 'Depth (meter)'),
            'beam' => Yii::t('app', 'Beam (meter)'),
            'draught' => Yii::t('app', 'Draught (meter)'),
            'engine_type' => Yii::t('app', 'Engine Type'),
            'fuel_type' => Yii::t('app', 'Fuel Type'),
            'number_of_cylinders' => Yii::t('app', 'Number Of Cylinders'),
            'propeller_diameter_pitch_no_of_blades' => Yii::t('app', 'Propeller Diameter Pitch No Of Blades'),
            'horse_power_of_engine' => Yii::t('app', 'Horse Power Of Engine'),
            'gear_ratio' => Yii::t('app', 'Gear Ratio'),
            'engine_model' => Yii::t('app', 'Engine Model'),
            'steering_gear_type' => Yii::t('app', 'Steering Gear Type'),
            'engine_number' => Yii::t('app', 'Engine Number'),
            'engine_model_number' => Yii::t('app', 'Engine Model Number'),
            'fuel_oil' => Yii::t('app', 'Fuel Oil (L)'),
            'fish_hold' => Yii::t('app', 'Fresh  Hold (L)'),
            'fresh_water' => Yii::t('app', 'Fresh Water (m³)'),
            'stores' => Yii::t('app', 'Stores'),
            'chilled_bath' => Yii::t('app', 'Chilled Bath (m³)'),
            'designed_speed' => Yii::t('app', 'Designed Speed'),
            'place_of_inspectio' => Yii::t('app', 'Place Of Inspection'),
            'inspection_date' => Yii::t('app', 'Inspection Date'),
            'vessel_at_the_time_of_inspection' => Yii::t('app', 'Where was the vessel at the time of inspection?'),
            'hull_hull_framing' => Yii::t('app', 'Hull Hull Framing'),
            'hatch_way_coamings' => Yii::t('app', 'Hatch Way Coamings'),
            'inlets_discharges' => Yii::t('app', 'Inlets Discharges'),
            'machinery_space_opening' => Yii::t('app', 'Machinery Space Opening'),
            'deck_deck_framing' => Yii::t('app', 'Deck Deck Framing'),
            'deck_opening' => Yii::t('app', 'Deck Opening'),
            'bulk_head' => Yii::t('app', 'Bulk Head'),
            'pipes_bunkering_inlets' => Yii::t('app', 'Pipes Bunkering Inlets'),
            'weather_tight_doors' => Yii::t('app', 'Weather Tight Doors'),
            'freeing_ports' => Yii::t('app', 'Freeing Ports'),
            'fishing_gear_symbol' => Yii::t('app', 'Fishing Gear Symbol'),
            'bow_height' => Yii::t('app', 'Bow Height'),
            'chilled_bath_partitions' => Yii::t('app', 'Chilled Bath Partitions'),
            'maximum_draught' => Yii::t('app', 'Maximum Draught'),
            'bait_hold_arrangement' => Yii::t('app', 'Bait Hold Arrangement'),
            'water_tanks_partitions' => Yii::t('app', 'Water Tanks Partitions'),
            'stores_cargo_hold_constructions' => Yii::t('app', 'Stores Cargo Hold Constructions'),
            'fuel_tanks_partitions' => Yii::t('app', 'Fuel Tanks Partitions'),
            'draught_marks' => Yii::t('app', 'Draught Marks'),
            'portable_fish_hold_divisions' => Yii::t('app', 'Portable Fish Hold Divisions'),
            'stability_notice' => Yii::t('app', 'Stability Notice'),
            'layout_of_machinery_space' => Yii::t('app', 'Layout Of Machinery Space'),
            'console_monitoring_instruments' => Yii::t('app', 'Console Monitoring Instruments'),
            'propulsion_machinery_steering_gear' => Yii::t('app', 'Propulsion Machinery Steering Gear'),
            'fuel_oil_installation' => Yii::t('app', 'Fuel Oil Installation'),
            'gear_marking' => Yii::t('app', 'Gear Marking'),
            'dehooker_line_cutter' => Yii::t('app', 'Dehooker Line Cutter'),
            'engine_console' => Yii::t('app', 'Engine Console'),
            'cooling_water_system' => Yii::t('app', 'Cooling Water System'),
            'lighting' => Yii::t('app', 'Lighting'),
            'bilge_pumping_systems' => Yii::t('app', 'Bilge Pumping Systems'),
            'floor' => Yii::t('app', 'Floor'),
            'exhaust_systems' => Yii::t('app', 'Exhaust Systems'),
            'ventilation' => Yii::t('app', 'Ventilation'),
            'hydraulic_system' => Yii::t('app', 'Hydraulic System'),
            'sound_vibration' => Yii::t('app', 'Sound Vibration'),
            'refrigeration_system' => Yii::t('app', 'Refrigeration System'),
            'engine_mounting' => Yii::t('app', 'Engine Mounting'),
            'main_source_electrical_supply' => Yii::t('app', 'Main Source Electrical Supply'),
            'earthing_bonding' => Yii::t('app', 'Earthing Bonding'),
            'electrical_system' => Yii::t('app', 'Electrical System'),
            'lighting_system' => Yii::t('app', 'Lighting System'),
            'direct_current_system' => Yii::t('app', 'Direct Current System'),
            'electric_motors' => Yii::t('app', 'Electric Motors'),
            'alternating_current_system' => Yii::t('app', 'Alternating Current System'),
            'conductors_nodes_breakers' => Yii::t('app', 'Conductors Nodes Breakers'),
            'storage_of_gas_cylinders' => Yii::t('app', 'Storage Of Gas Cylinders'),
            'means_of_escape' => Yii::t('app', 'Means Of Escape'),
            'firefighting_appliances' => Yii::t('app', 'Firefighting Appliances'),
            'fire_hydrants_fire_horses_nozzles' => Yii::t('app', 'Fire Hydrants Fire Horses Nozzles'),
            'ventilation_system' => Yii::t('app', 'Ventilation System'),
            'surfaces_of_deck' => Yii::t('app', 'Surfaces Of Deck'),
            'medical_facilities' => Yii::t('app', 'Medical Facilities'),
            'deck_opening_doors' => Yii::t('app', 'Deck Opening Doors'),
            'dangerous_areas' => Yii::t('app', 'Dangerous Areas'),
            'bulwark_rails_guards' => Yii::t('app', 'Bulwark Rails Guards'),
            'life_jackets_personal_flotation_devices' => Yii::t('app', 'Life Jackets Personal Flotation Devices'),
            'stairways_ladders' => Yii::t('app', 'Stairways Ladders'),
            'lifebuoys' => Yii::t('app', 'Lifebuoys'),
            'cooking_facilities' => Yii::t('app', 'Cooking Facilities'),
            'distress_signals' => Yii::t('app', 'Distress Signals'),
            'deck_machinery_tackles_lifting_gear' => Yii::t('app', 'Deck Machinery Tackles Lifting Gear'),
            'source_of_energy' => Yii::t('app', 'Source Of Energy'),
            'navigation_lights' => Yii::t('app', 'Navigation Lights'),
            'radio_installation_equipment' => Yii::t('app', 'Radio Installation Equipment'),
            'crew_accommodation' => Yii::t('app', 'Crew Accommodation'),
            'magnetic_compass' => Yii::t('app', 'Magnetic Compass'),
            'lighting_heating_ventilating' => Yii::t('app', 'Lighting Heating Ventilating'),
            'gps_satellite_navigation_system' => Yii::t('app', 'Gps Satellite Navigation System'),
            'sleeping_spaces' => Yii::t('app', 'Sleeping Spaces'),
            'means_depth_finding' => Yii::t('app', 'Means Depth Finding'),
            'eating_spaces_cooking_facilities' => Yii::t('app', 'Eating Spaces Cooking Facilities'),
            'nautical_instruments_publications' => Yii::t('app', 'Nautical Instruments Publications'),
            'sanitary_facilities' => Yii::t('app', 'Sanitary Facilities'),
            'signaling_system' => Yii::t('app', 'Signaling System'),
            'water_facilities' => Yii::t('app', 'Water Facilities'),
            'navigation_bridge_visibility' => Yii::t('app', 'Navigation Bridge Visibility'),
            'secondary_means_of_starting' => Yii::t('app', 'Secondary Means Of Starting'),
            'emergency_steering_arrangement' => Yii::t('app', 'Emergency Steering Arrangement'),
            'emergency_source_of_electrical_power' => Yii::t('app', 'Emergency Source Of Electrical Power'),
            'essential_machinery_spares_and_tools_required' => Yii::t('app', 'Does the vessel carry the essential machinery spares and tools required to carry out minor and/or emergency repairs'),
            'call_sign_number' => Yii::t('app', 'Call Sign Number'),
            'vms_number' => Yii::t('app', 'Vms Number'),
            'date_of_repairs_approved_modifications_hull' => Yii::t('app', 'Date Of Repairs Approved Modifications Hull'),
            'date_of_repairs_approved_modifications_machinery' => Yii::t('app', 'Date Of Repairs Approved Modifications Machinery'),
            'running_trail_carried_out_on' => Yii::t('app', 'Running Trail Carried Out On'),
            'machinery_operation' => Yii::t('app', 'Machinery Operation'),
            'maneuverability' => Yii::t('app', 'Maneuverability'),
            'declaration' => Yii::t('app', 'This is to certify that the above named/numbered vessel was dully inspected by me and found to be in a fit and seaworthy condition to operate as a within the limit of.'),
            'next_inspected_date' => Yii::t('app', 'Next Inspected Date'),
            'mea_certificate_number' => Yii::t('app', 'Mea Certificate Number'),
            'condition_hull_internal' => Yii::t('app', 'Condition Hull Internal'),
            'condition_hull_external' => Yii::t('app', 'Condition Hull External'),
            'condition_hull_sheathing' => Yii::t('app', 'Condition Hull Sheathing'),
            'condition_decks' => Yii::t('app', 'Condition Decks'),
            'condition_steering_gear' => Yii::t('app', 'Condition Steering Gear'),
            'condition_cargo_compartment' => Yii::t('app', 'Condition Cargo Compartment'),
            'condition_anchor_cables' => Yii::t('app', 'Condition Anchor Cables'),
            'condition_framework_timbers_internals' => Yii::t('app', 'Condition Framework Timbers Internals'),
            'condition_navigation_lights' => Yii::t('app', 'Condition Navigation Lights'),
            'condition_machinery' => Yii::t('app', 'Condition Machinery'),
            'condition_rudder' => Yii::t('app', 'Condition Rudder'),
            'condition_date_last_overhaul' => Yii::t('app', 'Condition Date Last Overhaul'),
            'equipped_compass' => Yii::t('app', 'Equipped Compass'),
            'equipped_bailers' => Yii::t('app', 'Equipped Bailers'),
            'equipped_life_saving_appliances' => Yii::t('app', 'Equipped Life Saving Appliances'),
            'equipped_first_aid_equipment' => Yii::t('app', 'Equipped First Aid Equipment'),
            'equipped_fire_extingulshers_type' => Yii::t('app', 'Equipped Fire Extingulshers Type'),
            'equipped_navigation_equipment' => Yii::t('app', 'Equipped Navigation Equipment'),
            'equipped_bilge_pump' => Yii::t('app', 'Equipped Bilge Pump'),
            'equipped_gps_available' => Yii::t('app', 'Equipped Gps Available'),
            'equipped_vms_installation' => Yii::t('app', 'Equipped Vms Installation'),
            'equipped_emergency_repairs' => Yii::t('app', 'Equipped Emergency Repairs'),
            'number_of_professional_competent_crew' => Yii::t('app', 'Number Of Professional Competent Crew'),
            'number_of_professional_competent_crew_minimum' => Yii::t('app', 'Number Of Professional Competent Crew Minimum'),
            'name_of_coxswain_certificate_no' => Yii::t('app', 'Serial No. of Coxswain Certificate No.'),
            'name_of_engine_driver_certificate_no' => Yii::t('app', 'Name Of Engine Driver Certificate No'),
            'other_information' => Yii::t('app', 'Other Information'),
            'value_of_boat_hull' => Yii::t('app', 'Value Of Boat Hull'),
            'value_of_boat_engine' => Yii::t('app', 'Value Of Boat Engine'),
            'compass' => Yii::t('app', 'Compass'),
            'radio' => Yii::t('app', 'Radio'),
            'radar' => Yii::t('app', 'Radar'),
            'ais' => Yii::t('app', 'Ais'),
            'winch' => Yii::t('app', 'Winch'),
            'gps' => Yii::t('app', 'Gps'),
            'vms' => Yii::t('app', 'Vms'),
            'vms_installation' => Yii::t('app', 'Vms Installation'),
            'gillnets' => Yii::t('app', 'Gillnets'),
            'longlines' => Yii::t('app', 'Longlines'),
            'other' => Yii::t('app', 'Other'),
            'total' => Yii::t('app', 'Total'),
            'ais_buoys' => Yii::t('app', 'Ais Buoys'),
            'created_by' => Yii::t('app', 'Created By'),
            'created' => Yii::t('app', 'Created'),
            'status' => Yii::t('app', 'Status'),
            'approval_stage' => Yii::t('app', 'Approval Stage'),
            'approved_time' => Yii::t('app', 'Approved Time'),
            'expire_date' => Yii::t('app', 'Expire Date'),
            'renew' => Yii::t('app', 'Renew'),
        ];
    }
}
