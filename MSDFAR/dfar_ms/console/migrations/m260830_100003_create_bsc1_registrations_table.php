<?php

use yii\db\Migration;

class m260830_100003_create_bsc1_registrations_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('bsc1_registrations', [
            'id' => $this->primaryKey()->unsigned(),
            'submission_id' => $this->integer()->unsigned()->notNull(),
            'action' => "ENUM('first','renewal','cancellation') NOT NULL",
            'craft_type' => "ENUM('imul_over50','imul','iday','ofrp','mtrb','ntrb','nbsb') NOT NULL",
            'reg_count' => $this->integer()->unsigned()->notNull()->defaultValue(0),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->addForeignKey(
            'fk_bsc1_registrations_submission', 'bsc1_registrations', 'submission_id',
            'bsc1_submissions', 'id', 'CASCADE'
        );
        $this->createIndex('uq_reg_action_craft', 'bsc1_registrations', ['submission_id', 'action', 'craft_type'], true);
    }

    public function safeDown()
    {
        $this->dropTable('bsc1_registrations');
    }
}