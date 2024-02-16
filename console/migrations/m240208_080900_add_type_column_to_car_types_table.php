<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%car_types}}`.
 */
class m240208_080900_add_type_column_to_car_types_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%car_types}}','type', $this->smallInteger()->defaultValue(booking\entities\Slot\Slot::TYPE_ADULT));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%car_types}}','type');
    }
}
