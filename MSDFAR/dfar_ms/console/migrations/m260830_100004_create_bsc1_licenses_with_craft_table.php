<?php

use yii\db\Migration;

class m260830_100004_create_bsc1_licenses_with_craft_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('bsc1_licenses_with_craft', [
            'id' => $this->primaryKey()->unsigned(),
            'submission_id' => $this->integer()->unsigned()->notNull(),
            'craft_type' => "ENUM('imul','iday','ofrp','mtrb','ntrb','nbsb') NOT NULL",
            'license_count' => $this->integer()->unsigned()->notNull()->defaultValue(0),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->addForeignKey(
            'fk_bsc1_lic_submission', 'bsc1_licenses_with_craft', 'submission_id',
            'bsc1_submissions', 'id', 'CASCADE'
        );
        $this->createIndex('uq_lic_craft', 'bsc1_licenses_with_craft', ['submission_id', 'craft_type'], true);
    }

    public function safeDown()
    {
        $this->dropTable('bsc1_licenses_with_craft');
    }
}