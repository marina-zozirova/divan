<?php

use yii\db\Migration;

/**
 * Class m231224_223123_alter_currency_table
 */
class m231224_223123_alter_currency_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231224_223123_alter_currency_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231224_223123_alter_currency_table cannot be reverted.\n";

        return false;
    }
    */
}
