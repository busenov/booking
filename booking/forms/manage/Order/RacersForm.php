<?php

namespace booking\forms\manage\Order;

use booking\entities\Car\CarType;
use booking\entities\Order\Order;
use booking\entities\Order\OrderItem;
use booking\entities\User\User;
use booking\forms\CompositeForm;
use yii\base\Model;
/**
 *
 * @property RacerForm[] $items
 */
class RacersForm extends CompositeForm
{
    public ?string $order_id;
    public function __construct(Order $order, $config = [])
    {
        $this->order_id=$order->id;
        $items=[];
        $additionalInfo=$order->additionalInfo;

        $racer=null;
        foreach ($order->items as $item) {
            for ($i=0;$i<$item->qty;$i++) {
                if (array_key_exists($item->slot->id,$additionalInfo)) {
                    $racer=array_shift($additionalInfo[$item->slot->id]);
                } else {
                    $racer=null;
                }
                if ($racer) {
                    $items[]=new RacerForm($item->slot,[
                        'name'=>$racer['name']??'',
                        'weight'=>$racer['weight']??'',
                        'height'=>$racer['height']??'',
                        'birthday'=>$racer['birthday']??'',
                    ]);
                } else {
                    $items[]=new RacerForm($item->slot);
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
            [['order_id'], 'integer'],
        ];
    }

    public function attributeLabels():array
    {
        return User::getAttributeLabels();
    }

    protected function internalForms(): array
    {
        return ['items'];
    }
}