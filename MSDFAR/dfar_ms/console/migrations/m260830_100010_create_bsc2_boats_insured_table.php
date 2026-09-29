<?php

use yii\db\Migration;

/**
 * New table, not part of the original design. Per the updated paper form:
 * "No of Fishing boat insured" — a craft-type breakdown (IMUL/IDAY/OFRP/
 * MTRB) that's a distinct concept from sea-worthiness certificates, not a
 * sub-category of it as originally assumed.
 */
class m260830_100010_create_bsc2_boats_insured_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('bsc2_boats_insured', [
            'id' => $this->primaryKey()->unsigned(),
            'submission_id' => $this->integer()->unsigned()->notNull(),
            'craft_type' => "ENUM('imul','iday','ofrp','mtrb') NOT NULL",
            'insured_count' => $this->integer()->unsigned()->notNull()->defaultValue(0),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->addForeignKey(
            'fk_bsc2_insured_submission', 'bsc2_boats_insured', 'submission_id',
            'bsc2_submissions', 'id', 'CASCADE'
        );
        $this->createIndex('uq_insured_craft', 'bsc2_boats_insured', ['submission_id', 'craft_type'], true);
    }

    public function safeDown()
    {
        $this->dropTable('bsc2_boats_insured');
    }
}