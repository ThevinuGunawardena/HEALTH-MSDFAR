<?php

use yii\db\Migration;

class m260830_100006_create_submission_audit_log_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('submission_audit_log', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'form_type' => "ENUM('bsc1','bsc2') NOT NULL",
            'submission_id' => $this->integer()->unsigned()->notNull(),
            'action' => "ENUM('create','update','submit','validate','return','reopen') NOT NULL",
            'field_changed' => $this->string(100)->null(),
            'old_value' => $this->string(255)->null(),
            'new_value' => $this->string(255)->null(),
            'changed_by' => $this->integer()->unsigned()->notNull(),
            'acted_as_admin' => $this->tinyInteger()->unsigned()->notNull()->defaultValue(0),
            'real_actor' => $this->integer()->unsigned()->null(),
            'changed_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->createIndex('idx_audit_submission', 'submission_audit_log', ['form_type', 'submission_id']);
    }

    public function safeDown()
    {
        $this->dropTable('submission_audit_log');
    }
}