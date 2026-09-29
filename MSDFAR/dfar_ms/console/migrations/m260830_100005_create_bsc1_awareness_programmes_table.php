<?php

use yii\db\Migration;

class m260830_100005_create_bsc1_awareness_programmes_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('bsc1_awareness_programmes', [
            'id' => $this->primaryKey()->unsigned(),
            'submission_id' => $this->integer()->unsigned()->notNull(),
            'event_date' => $this->date()->null(),
            'nature' => $this->string(255)->null(),
            'participants' => $this->integer()->unsigned()->defaultValue(0),
            'cost' => $this->decimal(10, 2)->defaultValue(0),
            'resource_person' => $this->string(150)->null(),
            'institution' => $this->string(150)->null(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->addForeignKey(
            'fk_bsc1_awareness_submission', 'bsc1_awareness_programmes', 'submission_id',
            'bsc1_submissions', 'id', 'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropTable('bsc1_awareness_programmes');
    }
}