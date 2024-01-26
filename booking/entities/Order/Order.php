<?php

namespace booking\entities\Order;

use booking\entities\behaviors\FillingServiceFieldsBehavior;
use booking\entities\behaviors\LoggingBehavior;
use booking\entities\Car\CarType;
use booking\entities\Slot\Slot;
use booking\entities\User\User;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "car_types".
 *
 * @property int $id
 * @property int $slot_id
 * @property int $customer_id
 * @property int|null $status
 * @property bool $isChild
 *
 * @property string|null $note
 *
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $author_id
 * @property string|null $author_name
 * @property int|null $editor_id
 * @property string|null $editor_name
 *
 * @property OrderItem[] $items
 * @property User $customer
 * @property Slot $slot
 */
class Order extends ActiveRecord
{
    const STATUS_NEW=10;                    //Новый
    const STATUS_AWAITING_PAYMENT=20;       //Ожидает оплаты
    const STATUS_PAID=30;                   //Оплачен
    const STATUS_COMPLETED=40;              //Завершен
    const STATUS_DELETED=100;               //Удален

    public static function create(
                                int     $slotId,
                                int     $status=self::STATUS_NEW,
                                ?bool     $isChild = false,
                                ?string $note=null
                            ):self
    {
        return new self([
            'slot_id'=>$slotId,
            'status'=>$status,
            'isChild'=>$isChild,
            'note'=>$note
        ]);
    }
    public function edit(
        int     $status=self::STATUS_NEW,
        ?bool     $isChild = false,
        ?string $note=null
    ):void
    {
        $this->status=$status;
        $this->isChild=$isChild;
        $this->note=$note;

    }
#on
    public function onNew()
    {
        $this->status=self::STATUS_NEW;
    }
    public function onAwaitingPayment()
    {
        $this->status=self::STATUS_AWAITING_PAYMENT;
    }
    public function onPaid()
    {
        $this->status=self::STATUS_PAID;
    }
    public function onCompleted()
    {
        $this->status=self::STATUS_COMPLETED;
    }
    public function onDeleted()
    {
        $this->status=self::STATUS_DELETED;
    }
#is
    public function isNew():bool
    {
        return $this->status===self::STATUS_NEW;
    }
    public function isAwaitingPayment():bool
    {
        return $this->status===self::STATUS_AWAITING_PAYMENT;
    }
    public function isPaid():bool
    {
        return $this->status===self::STATUS_PAID;
    }
    public function isCompleted():bool
    {
        return $this->status===self::STATUS_COMPLETED;
    }
    public function isDeleted():bool
    {
        return $this->status===self::STATUS_DELETED;
    }
#gets
    public function getItems(): ActiveQuery
    {
        return $this->hasMany(OrderItem::class, ['order_id' => 'id']);
    }
    public function getCustomer(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'customer_id']);
    }
    public function getSlot(): ActiveQuery
    {
        return $this->hasOne(Slot::class, ['id' => 'slot_id']);
    }
#sets
    public function setCustomer(User $customer)
    {
        $this->customer=$customer;
    }
    public function addItem($carTypeId,int $qty=1): void
    {
        $items = $this->items;
        $notInOrder = true;
        foreach ($items as $item) {
            if ($item->carType->isIdEqualTo($carTypeId)) {
                $item->qty += $qty;
                $notInOrder = false;
            }
        }
        if ($notInOrder)
            $items[] = OrderItem::create($carTypeId,$qty);

        $this->items = $items;
    }
    public function editItem($item_id,int $qty=1): void
    {
        $items = $this->items;
        foreach ($items as $item) {
            if ($item->isIdEqualTo($item_id)) {
                $item->qty = $qty;
            }
        }
        $this->items = $items;
    }
    public function removeItem($id): void
    {
        $items = $this->items;
        foreach ($items as $i => $item) {
            if ($item->isIdEqualTo($id)) {
                unset($items[$i]);
                $this->items = $items;
                return;
            }
        }

    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{orders}}';
    }
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            FillingServiceFieldsBehavior::class,
            [
                'class' => LoggingBehavior::class,
            ],
            [
                'class' => SaveRelationsBehavior::class,
                'relations' => ['items'],
            ],
        ];
    }
    public function transactions(): array
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
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
            'id' => 'ID',
            'slot_id' => 'Слот',
            'customer_id' => 'Заказчик',
            'status' => 'Статус',
            '$isChild' => 'Детский?',
            'note' => 'Примечание',

            'created_at' => 'Создано',
            'updated_at' => 'Отредактировано',
            'author_id' => 'Автор',
            'author_name' => 'Автор',
            'editor_id' => 'Редактор',
            'editor_name' => 'Редактор',
        ];
    }
    public static function getStatusList(): array
    {
        return [
            self::STATUS_NEW => 'Новый',
            self::STATUS_AWAITING_PAYMENT => 'Ожидает оплаты',
            self::STATUS_PAID => 'Оплачен',
            self::STATUS_COMPLETED => 'Завершен',
            self::STATUS_DELETED => 'Удален',
        ];
    }

    public static function statusName($status): string
    {
        return ArrayHelper::getValue(self::getStatusList(), $status);
    }


}