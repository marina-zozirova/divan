<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%balance}}`.
 */
class m231224_200815_create_balance_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%balance}}', [
            'id' => $this->primaryKey(),
            'bank_account_id' => $this->integer()->comment('ID банковского счета'),
            'currency_id' => $this->integer()->comment('ID валюты'),
            'balancing' => $this->integer()->comment('Баланс'),
            'is_main' => $this->boolean()->defaultValue(false)->comment('Основной?'),
            'is_active' => $this->boolean()->defaultValue(true)->comment('Активный?'),
            'created_at' => $this->timestamp()->comment('Дата создания'),
            'updated_at' => $this->timestamp()->comment('Дата обновления')

        ]);

        $this->addForeignKey('fk_balance-bank_account', 'balance', 'bank_account_id',
            'bank_account', 'id');
        $this->addForeignKey('fk_balance-currency', 'balance', 'currency_id',
            'currency', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_balance-currency', 'currency');
        $this->dropForeignKey('fk_balance-bank_account', 'bank_account');
        $this->dropTable('{{%balance}}');
    }
}
