<?php

namespace booking\entities\Order;

use booking\entities\behaviors\FillingServiceFieldsBehavior;
use booking\entities\behaviors\LoggingBehavior;
use booking\entities\Car\CarType;
use booking\entities\Slot\Slot;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "order_items".
 *
 * @property int $id
 * @property int $order_id
 * @property int $slot_id
 * @property int $carType_id
 * @property int $qty
 * @property float $price
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
 * @property Slot $slot
 * @property float $total
 */
class OrderItem extends ActiveRecord
{

    public static function create(
                                int     $slot_id,
                                int     $carType_id,
                                int     $qty
                            ):self
    {
        return new self([
            'slot_id'   => $slot_id,
            'carType_id'=> $carType_id,
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
    public function isIdEqualTo(int $id):bool
    {
        return ($this->id === $id) ;
    }
    public function isIdSlotIdEqualTo(int $slotId,int $cartTypeId):bool
    {
        return (($this->carType_id == $cartTypeId) and ($this->slot_id==$slotId));
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
    public function getSlot(): ActiveQuery
    {
        return $this->hasOne(Slot::class, ['id' => 'slot_id']);
    }
    public function getPrice():float
    {
        return 1000;
    }
    public function getTotal():?float
    {
        return $this->price*$this->qty;
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
            'slot_id' => 'Заезд',
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