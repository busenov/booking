<?php

use yii\db\Migration;

/**
 * Class m240229_104431_change_token_columnt_credentials_table
 */
class m240229_104431_change_token_columnt_credentials_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%amocrm_credentials}}','token',$this->text()->notNull());
        $this->alterColumn('{{%amocrm_credentials}}','refresh_token',$this->text()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%amocrm_credentials}}','token',$this->string()->notNull());
        $this->alterColumn('{{%amocrm_credentials}}','refresh_token',$this->string()->notNull());
    }


}
