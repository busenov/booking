<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%users}}`.
 */
class m240115_054502_add_columns_to_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%users}}','name', $this->string());
        $this->addColumn('{{%users}}','surname', $this->string());
        $this->addColumn('{{%users}}','patronymic', $this->string());
        $this->addColumn('{{%users}}','telephone', $this->string());
        $this->addColumn('{{%users}}','gender', $this->smallInteger());
        $this->addColumn('{{%users}}', 'email_confirm_token', $this->string()->unique()->after('email'));

        $this->alterColumn('{{%users}}','id',$this->integer()->unsigned().' NOT NULL AUTO_INCREMENT');

        $this->addColumn('{{%users}}','author_id', $this->integer()->unsigned());
        $this->addColumn('{{%users}}','author_name', $this->string());
        $this->addColumn('{{%users}}','editor_id', $this->integer()->unsigned());
        $this->addColumn('{{%users}}','editor_name',  $this->string());

        $this->addForeignKey('{{%fk-users-author_id}}', '{{%users}}', 'author_id', '{{%users}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('{{%fk-users-editor_id}}', '{{%users}}', 'editor_id', '{{%users}}', 'id', 'SET NULL', 'CASCADE');

        $this->dropColumn('{{%users}}','username');



    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%users}}','name');
        $this->dropColumn('{{%users}}','surname');
        $this->dropColumn('{{%users}}','patronymic');
        $this->dropColumn('{{%users}}','telephone');
        $this->dropColumn('{{%users}}','gender');
        $this->dropColumn('{{%users}}','email_confirm_token');

        $this->dropForeignKey('{{%fk-users-author_id}}', '{{%users}}');
        $this->dropForeignKey('{{%fk-users-editor_id}}', '{{%users}}');

        $this->alterColumn('{{%users}}','id',$this->integer().' NOT NULL AUTO_INCREMENT');

        $this->dropColumn('{{%users}}','author_id');
        $this->dropColumn('{{%users}}','author_name');
        $this->dropColumn('{{%users}}','editor_id');
        $this->dropColumn('{{%users}}','editor_name');

        $this->addColumn('{{%users}}','username', $this->string()->notNull());


    }
}
