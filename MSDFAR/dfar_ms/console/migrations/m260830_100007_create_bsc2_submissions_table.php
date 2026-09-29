<?php

use yii\db\Migration;

/**
 * Revised against an updated paper version of BSC-2 shared by the
 * department (dated after the original spreadsheet this schema was first
 * built from). Changes from the original design:
 *   - explosive_cases / special_legal_actions / general_legal_actions
 *     REPLACED by no_of_raids / no_of_court_cases
 *   - child_savings_enrolled REMOVED (dropped from the current form)
 *   - recorded_marine_mammal_deaths / recorded_turtle_deaths ADDED (new)
 *   - Sea-worthiness certs simplified to inboard/outboard only — the
 *     IMUL/IDAY/OFRP/MTRB breakdown moves to a new bsc2_boats_insured
 *     table instead (see m260830_100010), since the new form shows these
 *     as two distinct concepts, not one.
 */
class m260830_100007_create_bsc2_submissions_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('bsc2_submissions', [
            'id' => $this->primaryKey()->unsigned(),
            'fi_division_id' => $this->integer()->unsigned()->notNull(),
            'period_date' => $this->date()->notNull(),

            'status' => $this->integer()->notNull()->defaultValue(1),
            'approval_stage' => $this->string(100)->notNull()->defaultValue(''),

            'was_returned' => $this->tinyInteger()->unsigned()->notNull()->defaultValue(0),
            'returned_by' => $this->integer()->unsigned()->null(),
            'returned_at' => $this->dateTime()->null(),
            'return_reason' => $this->string(255)->null(),

            'beach_seine_licenses' => $this->integer()->unsigned()->notNull()->defaultValue(0),

            'production_lagoon' => $this->decimal(10, 2)->notNull()->defaultValue(0),
            'production_coastal' => $this->decimal(10, 2)->notNull()->defaultValue(0),
            'production_offshore' => $this->decimal(10, 2)->notNull()->defaultValue(0),

            // Replaces explosive_cases / special_legal_actions / general_legal_actions
            'no_of_raids' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'no_of_court_cases' => $this->integer()->unsigned()->notNull()->defaultValue(0),

            'log_sheets_collected' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'departures' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'fishermen_registered' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'id_cards_issued' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'awareness_programmes' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'insurance_enrolled' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            // child_savings_enrolled removed — dropped from the current form
            'pension_enrolled' => $this->integer()->unsigned()->notNull()->defaultValue(0),

            // New — bycatch/environmental recording, not in the original design
            'recorded_marine_mammal_deaths' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'recorded_turtle_deaths' => $this->integer()->unsigned()->notNull()->defaultValue(0),

            'submitted_by' => $this->integer()->unsigned()->null(),
            'submitted_at' => $this->dateTime()->null(),
            'validated_by' => $this->integer()->unsigned()->null(),
            'validated_at' => $this->dateTime()->null(),
            'last_edited_by' => $this->integer()->unsigned()->null(),
            'last_edited_at' => $this->dateTime()->null(),
            'fi_submitted_snapshot' => $this->json()->null(),

            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->null()->defaultValue(null),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->createIndex('uq_bsc2_division_period', 'bsc2_submissions', ['fi_division_id', 'period_date'], true);
        $this->createIndex('idx_bsc2_period_status', 'bsc2_submissions', ['period_date', 'status']);
        $this->createIndex('idx_bsc2_approval_stage', 'bsc2_submissions', ['approval_stage']);
    }

    public function safeDown()
    {
        $this->dropTable('bsc2_submissions');
    }
}