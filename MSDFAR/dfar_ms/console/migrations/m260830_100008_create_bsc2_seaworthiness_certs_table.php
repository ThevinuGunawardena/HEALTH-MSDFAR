<?php

use yii\db\Migration;

/**
 * Simplified per the updated paper form: sea-worthiness certificates are
 * just Inboard/Outboard — the IMUL/IDAY/OFRP/MTRB craft-type breakdown
 * that was originally (incorrectly) merged into this table belongs to
 * boat insurance instead — see bsc2_boats_insured (m260830_100010).
 */
class m260830_100008_create_bsc2_seaworthiness_certs_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('bsc2_seaworthiness_certs', [
            'id' => $this->primaryKey()->unsigned(),
            'submission_id' => $this->integer()->unsigned()->notNull(),
            'category' => "ENUM('inboard','outboard') NOT NULL",
            'cert_count' => $this->integer()->unsigned()->notNull()->defaultValue(0),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->addForeignKey(
            'fk_bsc2_sw_submission', 'bsc2_seaworthiness_certs', 'submission_id',
            'bsc2_submissions', 'id', 'CASCADE'
        );
        $this->createIndex('uq_sw_category', 'bsc2_seaworthiness_certs', ['submission_id', 'category'], true);
    }

    public function safeDown()
    {
        $this->dropTable('bsc2_seaworthiness_certs');
    }
}