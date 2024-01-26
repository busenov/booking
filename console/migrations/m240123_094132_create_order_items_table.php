<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order_items}}`.
 */
class m240123_094132_create_order_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('{{%order_items}}', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'order_id' => $this->bigInteger()->unsigned(),
            'carType_id' => $this->integer()->unsigned(),
            'qty' => $this->integer()->unsigned(),

            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
            'author_id' => $this->integer()->unsigned(),
            'author_name' => $this->string(),
            'editor_id' => $this->integer()->unsigned(),
            'editor_name' => $this->string(),
        ],$tableOptions);

        $this->addForeignKey('{{%fk-order_items-author_id}}', '{{%order_items}}', 'author_id', '{{%users}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('{{%fk-order_items-editor_id}}', '{{%order_items}}', 'editor_id', '{{%users}}', 'id', 'SET NULL', 'RESTRICT');

        $this->addForeignKey('{{%fk-order_items-order_id}}', '{{%order_items}}', 'order_id', '{{%orders}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('{{%fk-order_items-carType_id}}', '{{%order_items}}', 'carType_id', '{{%car_types}}', 'id', 'RESTRICT', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%order_items}}');
    }
}
