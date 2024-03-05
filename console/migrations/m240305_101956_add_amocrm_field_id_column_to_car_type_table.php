<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%car_type}}`.
 */
class m240305_101956_add_amocrm_field_id_column_to_car_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%car_types}}','amocrm_field_id', $this->bigInteger()->unsigned());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%car_types}}','amocrm_field_id');
    }
}
