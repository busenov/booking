<?php

namespace booking\forms\manage\Order;

use booking\entities\Car\CarType;
use booking\entities\Order\OrderItem;
use booking\entities\User\User;
use yii\base\Model;

class OrderItemForm extends Model
{
    public ?int $cartTypeId=null;
    public ?string $qty=null;
    public ?CarType $_carType=null;
    public ?OrderItem $_orderItem;
    public function __construct(OrderItem $orderItem=null, $config = [])
    {
        parent::__construct($config);
        if ($orderItem) {
            $this->qty=$orderItem->qty;
            $this->_orderItem = $orderItem;
        }

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qty','cartTypeId',], 'integer'],
            ['qty', 'integer', 'min' => 1],
//            [['cartTypeId','qty'],'required']
        ];
    }

    public function attributeLabels():array
    {
        return OrderItem::getAttributeLabels();
    }

}