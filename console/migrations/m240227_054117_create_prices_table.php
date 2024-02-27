<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%prices}}`.
 */
class m240227_054117_create_prices_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('{{%car_prices}}', [
            'id' => $this->primaryKey(),
            'car_type_id' => $this->integer()->unsigned(),
            'weekday' => $this->smallInteger()->unsigned(),
            'date_from' => $this->integer()->unsigned(),
            'status' => $this->smallInteger()->unsigned(),
            'cost' => $this->double()->unsigned(),
            'note' => $this->string(),

            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
            'author_id' => $this->integer()->unsigned(),
            'author_name' => $this->string(),
            'editor_id' => $this->integer()->unsigned(),
            'editor_name' => $this->string()

        ],$tableOptions);

        $this->addForeignKey('{{%fk-car_prices-author_id}}', '{{%car_prices}}', 'author_id', '{{%users}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('{{%fk-car_prices-editor_id}}', '{{%car_prices}}', 'editor_id', '{{%users}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('{{%fk-car_prices-car_type_id}}', '{{%car_prices}}', 'car_type_id', '{{%car_types}}', 'id', 'SET NULL', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%car_prices}}');
    }
}
