<?php

namespace booking\forms\manage\Order;

use booking\entities\Car\CarType;
use booking\entities\Order\OrderItem;
use booking\entities\Slot\Slot;
use booking\entities\User\User;
use yii\base\Model;

class OrderItemForm extends Model
{
    public ?int $slot_id=null;
    public ?int $carType_id=null;
    public ?string $qty=null;
    public ?CarType $_carType=null;
    public ?OrderItem $_orderItem=null;
    public int $max;
    public function __construct(OrderItem $orderItem=null, $config = [])
    {
        parent::__construct($config);
        if ($orderItem) {
            $this->slot_id=$orderItem->slot_id;
            $this->qty=$orderItem->qty;
            $this->carType_id=$orderItem->carType_id;

            $this->_carType=$orderItem->carType;
            $this->_orderItem = $orderItem;
        }

        if ($this->_orderItem) {
            $this->max=$this->_orderItem->slot->getFree($this->_carType->id)+max($this->_orderItem->qty,$this->_carType->qty);
        } else {
            $this->max=$this->_carType->qty;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qty','cartTypeId','slotId'], 'integer'],
            [['slotId'], 'exist', 'skipOnError' => true, 'targetClass' => Slot::class, 'targetAttribute' => ['slot_id' => 'id']],
            ['qty', 'integer', 'min' => 0,'max'=>$this->max],
//            [['cartTypeId','qty'],'required']
        ];
    }

    public function attributeLabels():array
    {
        return OrderItem::getAttributeLabels();
    }

    public function getMax():int
    {
        return $this->max;
    }
}