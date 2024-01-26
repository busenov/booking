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
 * @property int $customer_id
 * @property int $status
 * @property int $isChild
 * @property string $note
 *
 * @property CustomerForm $customer
 * @property OrderItemForm[] $items
 */

class OrderCreateForm extends CompositeForm
{
    public ?int $slot_id=null;
    public ?string $note=null;
    public ?int $status=null;
    public ?bool $isChild=null;
    public function __construct( $config = [])
    {
        parent::__construct($config);
        $this->status=Order::STATUS_NEW;
        $this->customer=new CustomerForm();
        $this->items=[];
        $this->items=array_map(function (CarType $carType){
            return new OrderItemForm(null,[
                'cartTypeId'=>$carType->id,
                '_carType'=>$carType
            ]);
        },CarTypeRepository::findActive_st());

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['note'], 'string', 'max' => 255],
            [['isChild'], 'boolean'],
            [['slot_id'], 'exist', 'skipOnError' => true, 'targetClass' => Slot::class, 'targetAttribute' => ['slot_id' => 'id']],
            [['status'], 'in', 'range' => array_keys(Order::getStatusList())],
            [['slot_id','qty','status'],'required']
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