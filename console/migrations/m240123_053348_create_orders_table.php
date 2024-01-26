<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%orders}}`.
 */
class m240123_053348_create_orders_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('{{%orders}}', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'slot_id' => $this->bigInteger()->unsigned(),
            'customer_id' => $this->integer()->unsigned(),
            'status' => $this->smallInteger()->unsigned(),
            'isChild' => $this->boolean(),
            'note' => $this->string(),

            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
            'author_id' => $this->integer()->unsigned(),
            'author_name' => $this->string(),
            'editor_id' => $this->integer()->unsigned(),
            'editor_name' => $this->string(),
        ],$tableOptions);

        $this->addForeignKey('{{%fk-orders-author_id}}', '{{%orders}}', 'author_id', '{{%users}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('{{%fk-orders-editor_id}}', '{{%orders}}', 'editor_id', '{{%users}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('{{%fk-orders-customer_id}}', '{{%orders}}', 'customer_id', '{{%users}}', 'id', 'RESTRICT', 'RESTRICT');

        $this->addForeignKey('{{%fk-orders-slot_id}}', '{{%orders}}', 'slot_id', '{{%slots}}', 'id', 'RESTRICT', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%orders}}');
    }
}
