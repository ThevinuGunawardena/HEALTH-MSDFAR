<?php

use yii\db\Migration;

class m260917_093550_create_bsc1_submission_extensions_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%bsc1_submission_extensions}}', [
            'id' => $this->primaryKey(),

            'fi_division_id' => $this->integer()->notNull(),

            'period_date' => $this->date()->notNull(),

            'granted_by' => $this->integer()->notNull(),

            'granted_at' => $this->dateTime()->notNull(),

            'extension_deadline' => $this->dateTime()->notNull(),

            'created_at' => $this->dateTime()->notNull(),

            'updated_at' => $this->dateTime()->null(),
        ]);

        // FI Division
        $this->addForeignKey(
            'fk-bsc1-extension-division',
            '{{%bsc1_submission_extensions}}',
            'fi_division_id',
            '{{%m_division}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // AD/User who granted the extension
        $this->addForeignKey(
            'fk-bsc1-extension-granted-by',
            '{{%bsc1_submission_extensions}}',
            'granted_by',
            '{{%user}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        // Only one extension per division and reporting period
        $this->createIndex(
            'uq-bsc1-extension-division-period',
            '{{%bsc1_submission_extensions}}',
            ['fi_division_id', 'period_date'],
            true
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%bsc1_submission_extensions}}');
    }
}