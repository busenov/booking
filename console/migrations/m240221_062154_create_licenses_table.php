<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%licenses}}`.
 */
class m240221_062154_create_licenses_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('{{%licenses}}', [
            'id' => $this->primaryKey()->unsigned(),
            'number' => $this->integer()->unsigned(),
            'date' => $this->integer()->unsigned(),
            'user_id' => $this->integer()->unsigned(),
            'status' => $this->smallInteger()->unsigned(),
            'note' => $this->string(),

            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
            'author_id' => $this->integer()->unsigned(),
            'author_name' => $this->string(),
            'editor_id' => $this->integer()->unsigned(),
            'editor_name' => $this->string(),
        ],$tableOptions);

        $this->addForeignKey('{{%fk-licenses-author_id}}', '{{%licenses}}', 'author_id', '{{%users}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('{{%fk-licenses-editor_id}}', '{{%licenses}}', 'editor_id', '{{%users}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('{{%fk-licenses-user_id}}', '{{%licenses}}', 'user_id', '{{%users}}', 'id', 'SET NULL', 'RESTRICT');

        $this->createIndex('{{%idx-licenses-user_id}}', '{{%licenses}}', 'number',true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%licenses}}');
    }
}
