<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%amocrm_credentials}}`.
 */
class m240228_055022_create_amocrm_credentials_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('{{%amocrm_credentials}}', [
            'id' => $this->primaryKey(),
            'domain' => $this->string()->notNull(),
            'token' => $this->string()->notNull(),
            'refresh_token' => $this->string()->notNull(),
            'expires' => $this->integer()->unsigned()->notNull(),
            'client_id' => $this->string()->notNull(),
            'client_secret' => $this->string()->notNull(),
            'redirect_uri' => $this->string()->notNull(),

            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
            'author_id' => $this->integer()->unsigned(),
            'author_name' => $this->string(),
            'editor_id' => $this->integer()->unsigned(),
            'editor_name' => $this->string(),

        ],$tableOptions);

        $this->addForeignKey('{{%fk-amocrm_credentials-author_id}}', '{{%amocrm_credentials}}', 'author_id', '{{%users}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('{{%fk-amocrm_credentials-editor_id}}', '{{%amocrm_credentials}}', 'editor_id', '{{%users}}', 'id', 'SET NULL', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%amocrm_credentials}}');
    }
}
