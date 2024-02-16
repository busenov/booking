<?php
namespace booking\repositories;

use booking\entities\Order\Order;
use booking\entities\Order\OrderItem;
use booking\entities\Slot\Slot;
use booking\helpers\DateHelper;
use Yii;
use yii\caching\TagDependency;
use yii\db\ActiveRecord;
use yii\db\Query;

class OrderRepository
{

    public static function get_st($entityOrIdOrGuid): Order
    {
        if (is_a($entityOrIdOrGuid,Order::class)) {
            return $entityOrIdOrGuid;
        } else if (is_int($entityOrIdOrGuid)) {
            return static::getBy(['id' => $entityOrIdOrGuid]);
        } else {
            return static::getBy(['guid' => $entityOrIdOrGuid]);
        }
    }
    public function get($entityOrIdOrGuid): Order
    {
        return static::get_st($entityOrIdOrGuid);
    }
    public function getItem($itemOrItemId):OrderItem
    {
        if (is_a($itemOrItemId,OrderItem::class)) {
            return $itemOrItemId;
        }

        if (!$orderItem = OrderItem::findOne($itemOrItemId)) {
            throw new NotFoundException('OrderItem is not found.');
        }
        return $orderItem;
    }
    public function save(Order $entity): void
    {
        if (!$entity->save()) {
            throw new \RuntimeException('Ошибка сохранения.');
        }
        TagDependency::invalidate(Yii::$app->cache, 'order');
    }

    public function remove(Order $entity): void
    {
        if (!$entity->delete()) {
            throw new \RuntimeException('Ошибка удаления.');
        }
        TagDependency::invalidate(Yii::$app->cache, 'order');
    }
###Finds
    public static function find_st($entityOrId):?Order
    {
        if (is_a($entityOrId,Order::class)) {
            return $entityOrId;
        } else {
            return Order::findOne($entityOrId);
        }
    }
    public function find($entityOrId):?Order
    {
        return static::find_st($entityOrId);
    }

    /**
     * Кол-во зарезервированных машин. Если переданы slotId и carTypeId, тогда возвращаем число, иначе массив вида
     * [
     *  slotId=>
     *      1=>*кол-во зарезервинованных*
     *      2=>*кол-во зарезервинованных*
     *      ...
     *      'qty'=>*общее число по слоту*
     * ]
     * @param int|null $slotId
     * @param int|null $carTypeId
     * @return array|null|int
     */
    public static function findSumReservedCar_st(?int $slotId=null, ?int $carTypeId=null)
    {
        $statuses=[
            Order::STATUS_NEW,
            Order::STATUS_AWAITING_PAYMENT,
            Order::STATUS_PAID,
            Order::STATUS_COMPLETED,
        ];
        $query = new Query;
        $query
            ->select('items.slot_id as slot_id,carType_id,sum(items.qty) as sum')
            ->from(OrderItem::tableName().' as items')
            ->leftJoin([Order::tableName()=>'orders'],'orders.id=order_id')
            ->leftJoin([Slot::tableName()=>'slots'],'slots.id=items.slot_id')
            ->andWhere(['orders.status'=>$statuses])
//            ->andWhere(['>=','slots.date',DateHelper::beginDay()])
        ;

        $groupColumn=[];
        $groupColumn[]='slot_id';
        $groupColumn[]='carType_id';
        if ($slotId) {
            $query->andWhere(['items.slot_id'=>$slotId]);
        }
        if ($carTypeId) {
            $query->andWhere(['carType_id'=>$carTypeId]);
        }
        $result = $query->groupBy($groupColumn)->all();
        $result2=[];
        foreach ($result as $item) {
            if (empty($result2[$item['slot_id']])) {
                $result2[$item['slot_id']]=['qty'=>0];
            }
            if (empty($result2[$item['slot_id']][$item['carType_id']])) {
                $result2[$item['slot_id']][$item['carType_id']] = 0;
            }

            $result2[$item['slot_id']][$item['carType_id']] += $item['sum'];
            $result2[$item['slot_id']]['qty']+=$item['sum'];
        }

        if (($slotId) and ($carTypeId)) {
            return $result2[$slotId][$carTypeId];
        }
        return $result2;
    }
    public static function findSumReservedCar(?int $slotId=null, ?int $cartId=null):?int
    {
        return static::findSumReservedCar_st($slotId,$cartId);
    }
###other


    public function findAll():array
    {
        return $this->findAllBy([]);
    }
###

    private static function getBy(array $condition): Order
    {
        if (!$entity = Order::find()->andWhere($condition)->limit(1)->one()) {
            throw new NotFoundException('Заказ не найден');
        }
        return $entity;
    }




    private function findAllBy(array $condition):array
    {
        return Order::find()->andWhere($condition)->all();
    }

    private function findOneBy(array $condition):?Order
    {
        return Order::find()->andWhere($condition)->one();
    }
}