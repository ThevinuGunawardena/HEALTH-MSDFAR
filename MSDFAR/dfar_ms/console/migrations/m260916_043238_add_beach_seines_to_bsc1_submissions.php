<?php

use yii\db\Migration;

class m260916_043238_add_beach_seines_to_bsc1_submissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->addColumn(
            '{{%bsc1_submissions}}',
            'beach_seines',
            $this->integer()->notNull()->defaultValue(0)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(
            '{{%bsc1_submissions}}',
            'beach_seines'
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260916_043238_add_beach_seines_to_bsc1_submissions cannot be reverted.\n";

        return false;
    }
    */
}
