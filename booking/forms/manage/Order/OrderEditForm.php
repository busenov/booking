<?php

namespace booking\forms\manage\Order;

use booking\entities\Car\CarType;
use booking\entities\Order\Order;
use booking\entities\Slot\Slot;
use booking\forms\CompositeForm;
use yii\base\Model;

/**
 * @property int $slot_id
 * @property int $order_id
 * @property int $customer_id
 * @property int $status
 * @property string $note
 *
 * @property CustomerForm $customer
 * @property OrderItemForm[] $items
 */

class OrderEditForm extends CompositeForm
{
    public int $order_id;
    public ?int $slot_id=null;
    public ?string $note=null;
    public ?int $status=null;
    public Order $_order;
    public function __construct(Order $order, $config = [])
    {
        parent::__construct($config);
        $this->order_id=$order->id;
        $this->_order=$order;
        $this->customer=new CustomerForm();

//        $this->items=[];
//        $this->items=array_map(function (CarType $carType){
//            return new OrderItemForm(null,[
//                'cartTypeId'=>$carType->id,
//                '_carType'=>$carType
//            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['note'], 'string', 'max' => 255],
            [['isChild'], 'boolean'],
            [['status'], 'in', 'range' => array_keys(Order::getStatusList())],
            [['qty','status'],'required']
        ];
    }
    protected function internalForms(): array
    {
        return ['customer','items'];
    }
    public function attributeLabels():array
    {
        return Order::getAttributeLabels();
    }

}