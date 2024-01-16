<?php

use yii\db\Migration;

/**
 * Class m240116_114300_create_tables
 */
class m240116_114300_create_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('{{%slots}}', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'date' => $this->integer()->unsigned()->notNull(),
            'begin' => $this->smallInteger()->unsigned()->notNull(),
            'end' => $this->smallInteger()->unsigned()->notNull(),
            'qty' => $this->smallInteger()->unsigned()->notNull(),
            'status' => $this->smallInteger()->unsigned()->notNull(),
            'note' => $this->string(),

            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
            'author_id' => $this->integer()->unsigned(),
            'author_name' => $this->string(),
            'editor_id' => $this->integer()->unsigned(),
            'editor_name' => $this->string(),
        ], $tableOptions);

        $this->addForeignKey('{{%fk-slots-author_id}}', '{{%slots}}', 'author_id', '{{%users}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('{{%fk-slots-editor_id}}', '{{%slots}}', 'editor_id', '{{%users}}', 'id', 'SET NULL', 'RESTRICT');

        #car_types
        $this->createTable('{{%car_types}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string()->notNull(),
            'description' => $this->text(),
            'qty' => $this->smallInteger()->unsigned()->notNull(),
            'status' => $this->smallInteger()->unsigned()->notNull(),
            'pwr' => $this->double()->unsigned(),
            'note' => $this->string(),

            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
            'author_id' => $this->integer()->unsigned(),
            'author_name' => $this->string(),
            'editor_id' => $this->integer()->unsigned(),
            'editor_name' => $this->string(),
        ], $tableOptions);

        $this->addForeignKey('{{%fk-car_types-author_id}}', '{{%car_types}}', 'author_id', '{{%users}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('{{%fk-car_types-editor_id}}', '{{%car_types}}', 'editor_id', '{{%users}}', 'id', 'SET NULL', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%slots}}');
        $this->dropTable('{{%car_types}}');
    }

}
