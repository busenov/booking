<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%slots}}`.
 */
class m240207_081752_add_type_column_to_slots_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%slots}}','type', $this->smallInteger()->defaultValue(booking\entities\Slot\Slot::TYPE_ADULT));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%slots}}','type');
    }
}
