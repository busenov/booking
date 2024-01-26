<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%slots}}`.
 */
class m240125_060927_add_isChild_column_to_slots_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%slots}}','is_child', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%slots}}','is_child');
    }
}
