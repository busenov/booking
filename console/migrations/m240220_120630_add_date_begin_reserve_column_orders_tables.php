<?php

use yii\db\Migration;

/**
 * Class m240220_120630_add_date_begin_reserve_column_orders_tables
 */
class m240220_120630_add_date_begin_reserve_column_orders_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%orders}}','date_begin_reserve', $this->integer()->unsigned());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%orders}}','date_begin_reserve');
    }
}
