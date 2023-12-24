<?php

use yii\db\Migration;

/**
 * Class m231224_203021_alter_balance_table
 */
class m231224_203021_alter_balance_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('balance', 'balancing', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('balance', 'balancing', $this->integer());
    }
}
