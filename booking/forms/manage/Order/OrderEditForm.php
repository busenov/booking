<?php

namespace booking\forms\manage\Order;

use booking\entities\Car\CarType;
use booking\entities\Order\Order;
use booking\entities\Slot\Slot;
use booking\forms\CompositeForm;
use yii\base\Model;

/**
 * @property int $slot_id
 * @property int $customer_id
 * @property int $status
 * @property int $isChild
 * @property string $note
 *
 * @property CustomerForm $customer
 * @property OrderItemForm[] $items
 */

class OrderEditForm extends CompositeForm
{
    public ?string $note=null;
    public ?int $status=null;
    public ?bool $isChild=null;
    public Order $_order;
    public function __construct(Order $order, $config = [])
    {
        parent::__construct($config);
        $this->_order=$order;

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