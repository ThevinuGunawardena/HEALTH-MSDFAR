<?php

use yii\db\Migration;

class m260830_100009_create_bsc2_lagoon_activities_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('bsc2_lagoon_activities', [
            'id' => $this->primaryKey()->unsigned(),
            'submission_id' => $this->integer()->unsigned()->notNull(),
            'lagoon_name' => $this->string(150)->null(),
            'activity' => $this->text()->null(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->addForeignKey(
            'fk_bsc2_lagoon_submission', 'bsc2_lagoon_activities', 'submission_id',
            'bsc2_submissions', 'id', 'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropTable('bsc2_lagoon_activities');
    }
}