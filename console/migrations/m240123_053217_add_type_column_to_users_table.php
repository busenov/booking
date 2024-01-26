<?php

use booking\entities\User\User;
use yii\db\Migration;

/**
 * Handles adding columns to table `{{%users}}`.
 */
class m240123_053217_add_type_column_to_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%users}}','type', $this->smallInteger()->defaultValue(User::TYPE_USER));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%users}}','type');
    }
}
