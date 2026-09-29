<?php
use yii\db\Migration;

class m20250821_approval_stage_boat_cancel_transfer extends Migration
{
    public function safeUp()
    {
        // Add approval_stage to boat_number_cancel_requests
        $this->addColumn('boat_number_cancel_requests', 'approval_stage', $this->string(50)->defaultValue(null));
        // Add approval_stage to boat_number_transfer_request
        $this->addColumn('boat_number_transfer_request', 'approval_stage', $this->string(50)->defaultValue(null));
    }

    public function safeDown()
    {
        $this->dropColumn('boat_number_cancel_requests', 'approval_stage');
        $this->dropColumn('boat_number_transfer_request', 'approval_stage');
    }
}
