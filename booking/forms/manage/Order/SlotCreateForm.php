<?php

namespace booking\forms\manage\Order;

use booking\entities\Car\CarType;
use booking\entities\Order\Order;
use booking\entities\Slot\Slot;
use booking\forms\CompositeForm;
use booking\repositories\CarTypeRepository;
use yii\base\Model;

/**
 * @property int $slot_id
 *
 * @property OrderItemForm[] $items
 */

class SlotCreateForm extends CompositeForm
{
    public ?int $slot_id=null;
    public ?int $order_id=null;

    public ?Order $_order=null;
    public function __construct(Slot $slot, ?Order $order=null,$config = [])
    {
        parent::__construct($config);
        $this->items=[];
        $carTypeIdsUse=[];
        $items=[];
        if ($order) {

            $this->_order=$order;
            $this->order_id=$order->id;
            //добавляем существующие позиции
            foreach ($order->items as $item) {
                if ($slot->id!=$item->slot_id) continue;

                $items[] = new OrderItemForm($item);
                $carTypeIdsUse[]=$item->carType_id;
            }
        }
        //добавляем позиций которых нет в заказе
        foreach (CarTypeRepository::findActive_st() as $carType) {
            if (!in_array($carType->id, $carTypeIdsUse)) {
                if ($slot->type==$carType->type) {
                    $items[]=new OrderItemForm(null,[
                        'carType_id'=>$carType->id,
                        '_carType'=>$carType
                    ]);
                }
            }
        }
        $this->items=$items;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['slot_id'], 'exist', 'skipOnError' => true, 'targetClass' => Slot::class, 'targetAttribute' => ['slot_id' => 'id']],
            [['slot_id'],'required']
        ];
    }

    public function getItemsBySlotId(int $slot_id):array
    {
        $result=[];
        $carTypeUse=[];
        foreach ($this->items as $item) {
            if (($item->slot_id===$slot_id)) {
                $result[]=$item;
                $carTypeUse[]=$item->carType_id;
            }
        }
        foreach ($this->items as $item) {
            if ((empty($item->slot_id)) and (array_search($item->carType_id,$carTypeUse)===false)) {
                $item->slot_id=$slot_id;
                $result[]=$item;
            }
        }
        return $result;
    }

    protected function internalForms(): array
    {
        return ['items'];
    }
    public function attributeLabels():array
    {
        return Order::getAttributeLabels();
    }

}