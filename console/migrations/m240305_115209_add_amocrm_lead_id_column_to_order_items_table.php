<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%order_items}}`.
 */
class m240305_115209_add_amocrm_lead_id_column_to_order_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order_items}}','amocrm_lead_id', $this->bigInteger()->unsigned());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order_items}}','amocrm_lead_id');
    }
}
