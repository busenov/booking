<?php

use booking\entities\Order\Order;
use yii\db\Migration;

/**
 * Class m240208_091726_change_orders_order_items_tables
 */
class m240208_091726_change_orders_order_items_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //orders
        $this->dropForeignKey('{{%fk-orders-slot_id}}', '{{%orders}}');
        $this->dropColumn('{{%orders}}','slot_id');

        $this->addColumn('{{%orders}}','guid', $this->string(Order::GUID_LENGTH));
        $this->createIndex('{{%idx-orders-guid}}', '{{%orders}}', 'guid',true);

        //order_items
        $this->addColumn('{{%order_items}}','slot_id', $this->bigInteger()->unsigned());
        $this->addForeignKey('{{%fk-order_items-slot_id}}', '{{%order_items}}', 'slot_id', '{{%slots}}', 'id', 'RESTRICT', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        //orders
        $this->addColumn('{{%orders}}','slot_id', $this->bigInteger()->unsigned());
        $this->addForeignKey('{{%fk-orders-slot_id}}', '{{%orders}}', 'slot_id', '{{%slots}}', 'id', 'RESTRICT', 'RESTRICT');

        $this->dropIndex('{{%idx-orders-guid}}', '{{%orders}}');
        $this->dropColumn('{{%orders}}','guid');

        //order_items
        $this->dropForeignKey('{{%fk-order_items-slot_id}}', '{{%order_items}}');
        $this->dropColumn('{{%order_items}}','slot_id');
    }

}
