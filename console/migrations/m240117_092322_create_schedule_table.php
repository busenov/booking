<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%schedule}}`.
 */
class m240117_092322_create_schedule_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('{{%schedules}}', [
            'id' => $this->primaryKey()->unsigned(),
            'weekday' => $this->smallInteger()->unsigned(),
            'begin' => $this->integer()->unsigned()->notNull(),
            'end' => $this->integer()->unsigned()->notNull(),
            'interval' => $this->integer()->unsigned()->notNull(),
            'duration' => $this->integer()->unsigned()->notNull(),
            'status' => $this->smallInteger()->unsigned()->notNull(),
            'sort' => $this->smallInteger()->unsigned()->notNull(),
            'note' => $this->string(),

            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
            'author_id' => $this->integer()->unsigned(),
            'author_name' => $this->string(),
            'editor_id' => $this->integer()->unsigned(),
            'editor_name' => $this->string(),
        ],$tableOptions);

        $this->addForeignKey('{{%fk-schedules-author_id}}', '{{%schedules}}', 'author_id', '{{%users}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('{{%fk-schedules-editor_id}}', '{{%schedules}}', 'editor_id', '{{%users}}', 'id', 'SET NULL', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%schedules}}');
    }
}
