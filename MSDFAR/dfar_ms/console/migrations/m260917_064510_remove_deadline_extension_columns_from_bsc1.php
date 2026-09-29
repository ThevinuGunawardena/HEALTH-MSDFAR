<?php

use yii\db\Migration;

/**
 * Removes deadline extension columns from bsc1_submissions.
 *
 * Deadline extensions are tracked separately because an extension
 * can be granted before an FI creates a submission.
 */
class m260917_064510_remove_deadline_extension_columns_from_bsc1 extends Migration
{
    public function safeUp()
    {
        $this->dropColumn(
            'bsc1_submissions',
            'deadline_extension_granted_at'
        );

        $this->dropColumn(
            'bsc1_submissions',
            'deadline_extension_granted_by'
        );
    }

    public function safeDown()
    {
        $this->addColumn(
            'bsc1_submissions',
            'deadline_extension_granted_at',
            $this->dateTime()->null()
        );

        $this->addColumn(
            'bsc1_submissions',
            'deadline_extension_granted_by',
            $this->integer()->unsigned()->null()
        );
    }
}