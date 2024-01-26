<?php

namespace booking\entities\Order;

use booking\entities\behaviors\FillingServiceFieldsBehavior;
use booking\entities\behaviors\LoggingBehavior;
use booking\entities\Car\CarType;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "car_types".
 *
 * @property int $id
 * @property int $order_id
 * @property int $carType_id
 * @property int $qty
 *
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $author_id
 * @property string|null $author_name
 * @property int|null $editor_id
 * @property string|null $editor_name
 *
 * @property CarType $carType
 * @property Order $order
 */
class OrderItem extends ActiveRecord
{

    public static function create(
                                int     $carType_id,
                                int     $qty
                            ):self
    {
        return new self([
            'carType_id'=>$carType_id,
            'qty'=>     $qty
        ]);
    }
    public function edit(
                                int     $qty
    ):void
    {
        $this->qty=$qty;

    }
#iss
    public function isIdEqualTo($id):bool
    {
        return $this->id == $id;
    }
#gets
    public function getCarType(): ActiveQuery
    {
        return $this->hasOne(CarType::class, ['id' => 'carType_id']);
    }
    public function getOrder(): ActiveQuery
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{order_items}}';
    }
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            FillingServiceFieldsBehavior::class,
            [
                'class' => LoggingBehavior::class,
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels():array
    {
        return static::getAttributeLabels();
    }
    public static function getAttributeLabels():array
    {
        return [
            'order_id' => 'Заказ',
            'carType_id' => 'Машина',
            'qty' => 'Количество',

            'created_at' => 'Создано',
            'updated_at' => 'Отредактировано',
            'author_id' => 'Автор',
            'author_name' => 'Автор',
            'editor_id' => 'Редактор',
            'editor_name' => 'Редактор',
        ];
    }
}