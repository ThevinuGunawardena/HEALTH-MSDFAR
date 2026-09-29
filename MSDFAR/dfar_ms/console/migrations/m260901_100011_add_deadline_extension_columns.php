<?php

use yii\db\Migration;

/**
 * Adds deadline-extension tracking to bsc1_submissions and
 * bsc2_submissions, per confirmed rules:
 *   - FI has 5 days (of the following month) to submit, after which the
 *     return locks.
 *   - AD can grant ONE 48-hour extension per division, per missed period
 *     — never a second chance for that same month if the extension also
 *     lapses.
 *
 * INTERPRETATION FLAGGED: "can be done only once" is being read as once
 * per division PER MISSED MONTH (hence storing it on the submission row,
 * which is itself already unique per division+period) — not once ever,
 * permanently, across a division's whole history. If the intended rule
 * is a lifetime one-strike limit instead, this needs to move to a
 * division-level or a separate all-time tracking table.
 */
class m260901_100011_add_deadline_extension_columns extends Migration
{
    public function safeUp()
    {
        foreach (['bsc1_submissions', 'bsc2_submissions'] as $table) {
            $this->addColumn($table, 'deadline_extension_granted_at', $this->dateTime()->null());
            $this->addColumn($table, 'deadline_extension_granted_by', $this->integer()->unsigned()->null());
        }
    }

    public function safeDown()
    {
        foreach (['bsc1_submissions', 'bsc2_submissions'] as $table) {
            $this->dropColumn($table, 'deadline_extension_granted_at');
            $this->dropColumn($table, 'deadline_extension_granted_by');
        }
    }
}