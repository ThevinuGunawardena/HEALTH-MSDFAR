<?php

use yii\db\Migration;

class m260921_000002_remove_bsc2_old_extension_fields extends Migration
{
    public function safeUp()
    {
        $this->dropColumn(
            'bsc2_submissions',
            'deadline_extension_granted_at'
        );

        $this->dropColumn(
            'bsc2_submissions',
            'deadline_extension_granted_by'
        );
    }

    public function safeDown()
    {
        $this->addColumn(
            'bsc2_submissions',
            'deadline_extension_granted_at',
            $this->dateTime()->null()
        );

        $this->addColumn(
            'bsc2_submissions',
            'deadline_extension_granted_by',
            $this->integer()->null()
        );
    }
}