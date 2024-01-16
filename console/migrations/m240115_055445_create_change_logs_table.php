<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%change_logs}}`.
 */
class m240115_055445_create_change_logs_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('{{%change_logs}}', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'model_class' => $this->string(),
            'model_id' => $this->bigInteger()->unsigned(),
            'attribute' => $this->string(),
            'value_old' => $this->text(),
            'value_new' => $this->text(),
            'date_time' => $this->integer()->unsigned(),
            'model_json' => $this->text(),
            'action' => $this->smallInteger()->unsigned(),

            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
            'author_id' => $this->integer()->unsigned(),
            'author_name' => $this->string(),
            'editor_id' => $this->integer()->unsigned(),
            'editor_name' => $this->string(),
        ], $tableOptions);

        $this->addForeignKey('{{%fk-change_logs-author_id}}', '{{%change_logs}}', 'author_id', '{{%users}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('{{%fk-change_logs-editor_id}}', '{{%change_logs}}', 'editor_id', '{{%users}}', 'id', 'SET NULL', 'RESTRICT');

        $this->createIndex('{{%idx-change_logs-model}}','{{%change_logs}}',['model_class','model_id','attribute']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%change_logs}}');
    }
}
