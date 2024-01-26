<?php

use yii\db\Migration;

/**
 * Class m240125_112423_change_fk_order_id_to_items_table
 */
class m240125_112423_change_fk_order_id_to_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('{{%fk-order_items-order_id}}', '{{%order_items}}');
        $this->addForeignKey('{{%fk-order_items-order_id}}', '{{%order_items}}', 'order_id', '{{%orders}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-order_items-order_id}}', '{{%order_items}}');
        $this->addForeignKey('{{%fk-order_items-order_id}}', '{{%order_items}}', 'order_id', '{{%orders}}', 'id', 'RESTRICT', 'RESTRICT');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240125_112423_change_fk_order_id_to_items_table cannot be reverted.\n";

        return false;
    }
    */
}
