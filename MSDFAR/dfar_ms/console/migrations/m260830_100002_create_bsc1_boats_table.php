<?php

use yii\db\Migration;

class m260830_100002_create_bsc1_boats_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('bsc1_boats', [
            'id' => $this->primaryKey()->unsigned(),
            'submission_id' => $this->integer()->unsigned()->notNull(),
            'craft_type' => "ENUM('imul_over50','imul','iday','ofrp','mtrb','ntrb','nbsb') NOT NULL",
            'boat_count' => $this->integer()->unsigned()->notNull()->defaultValue(0),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->addForeignKey(
            'fk_bsc1_boats_submission', 'bsc1_boats', 'submission_id',
            'bsc1_submissions', 'id', 'CASCADE'
        );
        $this->createIndex('uq_boat_craft', 'bsc1_boats', ['submission_id', 'craft_type'], true);
    }

    public function safeDown()
    {
        $this->dropTable('bsc1_boats');
    }
}