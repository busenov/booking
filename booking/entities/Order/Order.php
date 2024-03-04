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
 * @property array|null $additionalInfo         //дополнительная информация по заказу(Например, инф по гонщикам(имя, вес, рост и т.д.))
 * @property array|null $additional_info_json
 * @property int|null $date_begin_reserve       //Дата начала резервирования
 *
 * @property OrderItem[] $items
 * @property User $customer
 * @property float $total
 */
class Order extends ActiveRecord
{
    const STATUS_NEW=10;                    //Новый
    const STATUS_RESERVATION_PROCESS=15;   //В процессе добавление заказов(должен длится не более TIME_RESERVE секунд, от начала добавления первого заказа
    const STATUS_CHECKOUT=20;               //Оформление. Ожидает оплаты
    const STATUS_PAID=30;                   //Оплачен
    const STATUS_SENT_AMOCRM=35;            //Отправлен в AmoCRM
    const STATUS_COMPLETED=40;              //Завершен
    const STATUS_DELETED=100;               //Удален
    const GUID_LENGTH=16;

    const COOKIE_NAME_GUID='orderGuid';
    const TIME_RESERVE=600;                 //Время бронирования

    public array $additionalInfo=[];

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
    public function onReserved()
    {
        $this->status=self::STATUS_RESERVATION_PROCESS;
        $this->date_begin_reserve=time();
    }
    public function onCheckout()
    {
        $this->status=self::STATUS_CHECKOUT;
    }
    public function onPaid()
    {
        $this->status=self::STATUS_PAID;
    }
    public function onSentAmoCRM()
    {
        $this->status=self::STATUS_SENT_AMOCRM;
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
    public function isReservationProcess():bool
    {
        return $this->status===self::STATUS_RESERVATION_PROCESS;
    }
    public function isCheckout():bool
    {
        return $this->status===self::STATUS_CHECKOUT;
    }
    public function isPaid():bool
    {
        return $this->status===self::STATUS_PAID;
    }
    public function isSentAmoCRM():bool
    {
        return $this->status===self::STATUS_SENT_AMOCRM;
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
    public function getName():string
    {
        return 'Заказ №'.$this->id;
    }
    public function getLeftTimeReserve():?int
    {
        if (($this->date_begin_reserve) and ($this->isReservationProcess())) {
            $passedTime=time()-$this->date_begin_reserve;
            if ($passedTime>self::TIME_RESERVE) {
                return null;
            } else {
                return self::TIME_RESERVE-$passedTime;
            }
        }
        return null;
    }
#sets
    public function setCustomer(User $customer)
    {
        $this->customer_id=$customer->id;
    }
#hass

    /**
     * @param OrderItem|int $itemOrItemId
     * @return bool
     */
    public function hasItem($itemOrItemId):bool
    {
        if (is_a($itemOrItemId,OrderItem::class)) {
            $itemOrItemId = $itemOrItemId->id;
        }
        foreach ($this->items as $item) {
            if ($item->isIdEqualTo($itemOrItemId)) {
                return true;
            }
        }
        return false;
    }
    public function changeItem(int $slotId, int $carTypeId, int $qty=0): void
    {
        if ($qty>0) {
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
        $result['leftTime']=$this->getLeftTimeReserve();
        $result['license_number']=(($this->customer) and ($this->customer->license))?$this->customer->license->number:'';

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
     * Проверка статуса заказа.
     * Если статус "процесс резервирования"(Order::STATUS_RESERVATION_PROCESS) длится более Order::TIME_RESERVE секунд
     * тогда меняем статус на Order::NEW, отменяем все брони по этому заказу
     * @return void
     */
    public function checkStatusReservationProcess():void
    {
        if ($this->isReservationProcess()) {
            if ($this->date_begin_reserve) {
                if ((time() - $this->date_begin_reserve) <= self::TIME_RESERVE) {
                    return;
                }
            }
            $this->revokeItems();
            $this->date_begin_reserve=null;
            $this->onNew();
            $this->save();
        }
    }
    /**
     * Проверка статуса заказа.
     * Если статус "процесс резервирования"(Order::STATUS_CHECKOUT) длится более Order::TIME_RESERVE секунд
     * тогда меняем статус на Order::NEW, отменяем все брони по этому заказу
     * @return void
     */
    public function checkStatusCheckout():void
    {
        if ($this->isCheckout()) {
            if ($this->date_begin_reserve) {
                if ((time() - $this->date_begin_reserve) <= self::TIME_RESERVE) {
                    return;
                }
            }
            $this->revokeItems();
            $this->date_begin_reserve=null;
            $this->onNew();
            $this->save();
        }
    }
    public function revokeItems():void
    {
        $this->items=[];
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
                'relations' => ['items','customer'],
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
            self::STATUS_RESERVATION_PROCESS => 'Заполняется',
            self::STATUS_CHECKOUT => 'Ожидает оплаты',
            self::STATUS_PAID => 'Оплачен',
            self::STATUS_COMPLETED => 'Завершен',
            self::STATUS_DELETED => 'Удален',
        ];
    }

    public static function statusName($status): string
    {
        return ArrayHelper::getValue(self::getStatusList(), $status);
    }

    public function totalBySlot(int $slot_id):float
    {
        foreach ($this->items as $item) {
            if ($item->slot->isIdEqualTo($slot_id)) {
                return $item->slot->total;
            }
        }
        return 0;
    }

    public function beforeSave($insert)
    {
        $this->additional_info_json=json_encode($this->additionalInfo);
        return parent::beforeSave($insert);
    }
    public function afterFind()
    {
        if ($this->additional_info_json) {
            $this->additionalInfo=json_decode($this->additional_info_json,true);
        }
        //Если статус "процесс резервирования"(Order::STATUS_RESERVATION_PROCESS) длится более Order::TIME_RESERVE секунд
        //тогда меняем статуся на Order::NEW, отменяем все брони по этому заказу
        $this->checkStatusReservationProcess();
        parent::afterFind();

    }




}