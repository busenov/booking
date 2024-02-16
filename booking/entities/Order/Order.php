<?php

namespace booking\entities\Order;

use booking\entities\behaviors\FillingServiceFieldsBehavior;
use booking\entities\behaviors\LoggingBehavior;
use booking\entities\Car\CarType;
use booking\entities\Slot\Slot;
use booking\entities\User\User;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property int $customer_id
 * @property int|null $status
 * @property bool $isChild
 *
 * @property string $note
 * @property string $guid
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
 * @property float $total
 */
class Order extends ActiveRecord
{
    const STATUS_NEW=10;                    //Новый
    const STATUS_AWAITING_PAYMENT=20;       //Ожидает оплаты
    const STATUS_PAID=30;                   //Оплачен
    const STATUS_COMPLETED=40;              //Завершен
    const STATUS_DELETED=100;               //Удален
    const GUID_LENGTH=16;

    const COOKIE_NAME_GUID='orderGuid';

    public static function create(
                                int     $status=self::STATUS_NEW,
                                ?string $note=null
                            ):self
    {
        return new self([
            'status'=>$status,
            'note'=>$note,
            'guid'=>Order::generateGuid(),
        ]);
    }
    public function edit(
        int     $status=self::STATUS_NEW,
        ?string $note=null
    ):void
    {
        $this->status=$status;
        $this->note=$note;

    }
    public static function generateGuid():string
    {
        do {
            $guid=strtolower(Yii::$app->security->generateRandomString(self::GUID_LENGTH));

        } while ((Order::findOne($guid)));
        return $guid;
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
        return $this->hasMany(OrderItem::class, ['order_id' => 'id'])->orderBy('slot_id');
    }
    public function getCustomer(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'customer_id']);
    }
    public function getQtyBySlotId(int $slotId):?int
    {
        $sum=0;
        foreach ($this->items as $item) {
            if ($item->slot->isIdEqualTo($slotId)) {
                $sum+=$item->qty;
            }
        }
        return $sum;
    }
    private ?float $_total=null;
    public function getTotal():?float
    {
        $total=0;
        if (!isset($this->_total)) {
            foreach ($this->items as $item) {
                $total+=$item->total;
            }
        }
        $this->_total=$total;
        return $this->_total;
    }
#sets
    public function setCustomer(User $customer)
    {
        $this->customer=$customer;
    }
#hass

    /**
     * @param OrderItem|int $item
     * @return bool
     */
    public function hasItem($itemOrItemId):bool
    {
        if (is_a($itemOrItemId,self::class)) {
            $itemOrItemId=$itemOrItemId->id;
        }
        foreach ($this->items as $item) {
            if ($item->isIdEqualTo($itemOrItemId->id)) {
                return true;
            }
        }
        return false;
    }
    public function changeItem(int $slotId, int $carTypeId, int $qty=0): void
    {
        $items = $this->items;
        $notInOrder = true;
        foreach ($items as $item) {
            if ($item->isIdSlotIdEqualTo($slotId,$carTypeId)) {
                $item->qty = $qty;
                $notInOrder = false;
            }
        }
        if ($notInOrder)
            $items[] = OrderItem::create($slotId,$carTypeId,$qty);

        $this->items = $items;
    }
    public function editItem($item_id,int $qty=0): void
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
     * Подготавливаем к JS, в виде массива
     * @return array
     */
    public function toJs():array
    {
        $result=$this->toArray();

        $items=[];
        foreach ($this->items as $item) {
            if (!array_key_exists($item->slot_id,$items)) {
                $items[$item->slot_id]=[];
                $items[$item->slot_id]['qty']=0;
            }
            if (!array_key_exists($item->carType_id,$items[$item->slot_id])) {
                $items[$item->slot_id][$item->carType_id]=[];
            }

            $items[$item->slot_id][$item->carType_id]['qty']=$item->qty;
            $items[$item->slot_id][$item->carType_id]['price']=$item->price;
            $items[$item->slot_id][$item->carType_id]['total']=$item->total;
            $items[$item->slot_id]['qty']+=$item->qty;
            $items[$item->slot_id]['total']=$item->slot->total;

        }
        $result['items']=$items;
        $result['total']=$this->total;
        return $result;
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
            'customer_id' => 'Заказчик',
            'status' => 'Статус',
            'isChild' => 'Детский?',
            'note' => 'Примечание',
            'guid' => 'GUID',

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

    public function totalBySlot(int $slot_id)
    {
        return 3000;
    }




}