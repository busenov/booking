<?php

use yii\db\Migration;

/**
 * Class m240115_054245_rename_user_table
 */
class m240115_054245_rename_user_table extends Migration
{
    public function up()
    {
        $this->renameTable('{{%user}}', '{{%users}}');
    }

    public function down()
    {
        $this->renameTable('{{%users}}', '{{%user}}');
    }
}
