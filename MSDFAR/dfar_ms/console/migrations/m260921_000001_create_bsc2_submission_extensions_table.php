<?php

use yii\db\Migration;

class m260921_000001_create_bsc2_submission_extensions_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%bsc2_submission_extensions}}', [
            'id' => $this->primaryKey(),

            'fi_division_id' => $this->integer()->notNull(),

            'period_date' => $this->date()->notNull(),

            'granted_by' => $this->integer()->notNull(),

            'granted_at' => $this->dateTime()->notNull(),

            'extension_deadline' => $this->dateTime()->notNull(),

            'created_at' => $this->dateTime()->notNull(),

            'updated_at' => $this->dateTime()->null(),
        ]);

        // One extension per FI division per reporting period
        $this->createIndex(
            'uq_bsc2_extension_division_period',
            '{{%bsc2_submission_extensions}}',
            ['fi_division_id', 'period_date'],
            true
        );

        // FI division
        $this->addForeignKey(
            'fk_bsc2_extension_division',
            '{{%bsc2_submission_extensions}}',
            'fi_division_id',
            '{{%m_division}}',
            'id',
            'CASCADE',
            'RESTRICT'
        );

        // AD who granted the extension
        $this->addForeignKey(
            'fk_bsc2_extension_granted_by',
            '{{%bsc2_submission_extensions}}',
            'granted_by',
            '{{%user}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey(
            'fk_bsc2_extension_granted_by',
            '{{%bsc2_submission_extensions}}'
        );

        $this->dropForeignKey(
            'fk_bsc2_extension_division',
            '{{%bsc2_submission_extensions}}'
        );

        $this->dropTable('{{%bsc2_submission_extensions}}');
    }
}