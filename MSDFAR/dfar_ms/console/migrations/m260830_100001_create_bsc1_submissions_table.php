<?php

use yii\db\Migration;

class m260830_100001_create_bsc1_submissions_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('bsc1_submissions', [
            'id' => $this->primaryKey()->unsigned(),
            'fi_division_id' => $this->integer()->unsigned()->notNull(),
            'period_date' => $this->date()->notNull(),

            'status' => $this->integer()->notNull()->defaultValue(1),
            'approval_stage' => $this->string(100)->notNull()->defaultValue(''),

            'was_returned' => $this->tinyInteger()->unsigned()->notNull()->defaultValue(0),
            'returned_by' => $this->integer()->unsigned()->null(),
            'returned_at' => $this->dateTime()->null(),
            'return_reason' => $this->string(255)->null(),

            'families' => $this->integer()->unsigned()->null(),
            'active_fishermen' => $this->integer()->unsigned()->null(),
            'population' => $this->integer()->unsigned()->null(),

            'migrated_imul' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'migrated_iday' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'migrated_ofrp' => $this->integer()->unsigned()->notNull()->defaultValue(0),

            'incident_partial_loss' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'incident_total_loss' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'incident_natural_deaths' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'incident_missing' => $this->integer()->unsigned()->notNull()->defaultValue(0),

            'licenses_without_craft' => $this->integer()->unsigned()->notNull()->defaultValue(0),

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

        $this->createIndex('uq_bsc1_division_period', 'bsc1_submissions', ['fi_division_id', 'period_date'], true);
        $this->createIndex('idx_bsc1_period_status', 'bsc1_submissions', ['period_date', 'status']);
        $this->createIndex('idx_bsc1_approval_stage', 'bsc1_submissions', ['approval_stage']);
    }

    public function safeDown()
    {
        $this->dropTable('bsc1_submissions');
    }
}