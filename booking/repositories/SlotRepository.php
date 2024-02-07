<?php
namespace booking\repositories;

use booking\entities\Slot\Slot;
use booking\helpers\DateHelper;
use Yii;
use yii\caching\TagDependency;
use yii\db\ActiveRecord;

class SlotRepository
{



    public static function get_st($entityOrId): Slot
    {
        if (is_a($entityOrId,Slot::class)) {
            return $entityOrId;
        } else {
            return static::getBy(['id' => $entityOrId]);
        }
    }
    public function get($entityOrId): Slot
    {
        return static::get_st($entityOrId);
    }

    public function save(Slot $entity): void
    {
        if (!$entity->save()) {
            throw new \RuntimeException('Ошибка сохранения.');
        }
        TagDependency::invalidate(Yii::$app->cache, 'carType');
    }

    public function remove(Slot $entity): void
    {
        if (!$entity->delete()) {
            throw new \RuntimeException('Ошибка удаления.');
        }
        TagDependency::invalidate(Yii::$app->cache, 'carType');
    }
###Finds
    public static function find_st($entityOrId):?Slot
    {
        if (is_a($entityOrId,Slot::class)) {
            return $entityOrId;
        } else {
            return Slot::findOne($entityOrId);
        }
    }
    public function find($entityOrId):?Slot
    {
        return static::find_st($entityOrId);
    }

###other
    /**
     * Получаем слоты на месяц разбитые по дням. Если не указан $unixtime, тогда текущее время
     * @param int|null $unixTime
     * @return array
     */
    public function getCalendar(?int $unixTime=null):array
    {
        $dateTime=$unixTime?:time();
        $slots=$this->findSlotsByMonth($dateTime);
        $calendar=[];
        $reservedCars=OrderRepository::findSumReservedCar_st();

        foreach ($slots as $slot) {
            $day=date('j',$slot->date);
            $year=date('Y',$slot->date);
            $free=isset($reservedCars[$slot->id])?($slot->qty - $reservedCars[$slot->id]['qty']):$slot->qty;
            $calendar[$year][$day][$slot->id]=[
                'begin'=>$slot->begin,
                'end'=>$slot->end,
                'qty'=>$slot->qty,
                'free'=>$free,
                'isChild'=>$slot->isChild(),
                'isClub'=>$slot->isClub(),
            ];
            if (isset($calendar[$year][$day]['qtySlot'])) {
                $calendar[$year][$day]['qtySlot']++;
            }else {
                $calendar[$year][$day]['qtySlot']=1;
            }

            if (isset($calendar[$year][$day]['qty'])) {
                $calendar[$year][$day]['qty']+=$free;
            }else {
                $calendar[$year][$day]['qty']=$free;
            }

        }
        return $calendar;
    }

    /**
     * Возвращаем массив с днями неделями. Так что 1 - это понедельник, 7 - воскресенье
     *
     * @param int|null $unixTime
     * @return array
     */
    public function calendarWeekly(?int $unixTime=null):array
    {
        $dateTime=$unixTime?:time();
        $slots=$this->findSlotsByWeek($dateTime);
        $calendar=[];
        $reservedCars=OrderRepository::findSumReservedCar_st();

        $beginWeek=DateHelper::beginWeekDayByUnixTime($dateTime);
        for ($i=1; $i<=7; $i++) {
            $calendar[$i]['unixTime']=$beginWeek+(($i-1)*60*60*24);
        }
        foreach ($slots as $slot) {
//            $day=date('j',$slot->date);
            $wDay=date('N',$slot->date);
//            $year=date('Y',$slot->date);
            $free=isset($reservedCars[$slot->id])?($slot->qty - $reservedCars[$slot->id]['qty']):$slot->qty;

            $calendar[$wDay][$slot->id]=[
                'begin'=>$slot->begin,
                'end'=>$slot->end,
                'qty'=>$slot->qty,
                'free'=>$free,
                'isChild'=>$slot->isChild(),
                'isClub'=>$slot->isClub(),
            ];
            if (isset($calendar[$wDay]['qtySlot'])) {
                $calendar[$wDay]['qtySlot']++;
            }else {
                $calendar[$wDay]['qtySlot']=1;
            }

            if (isset($calendar[$wDay]['qty'])) {
                $calendar[$wDay]['qty']+=$free;
            }else {
                $calendar[$wDay]['qty']=$free;
            }

        }
        return $calendar;
    }
    /**
     * @param int $dateTime
     * @return Slot[]
     * @throws \Exception
     */
    public function findSlotsByDay(int $dateTime):array
    {
        return Slot::find()
            ->andWhere([ '>=', 'date',DateHelper::beginDay($dateTime)])
            ->andWhere([ '<=', 'date',DateHelper::endDay($dateTime)])
            ->orderBy('date')
            ->all();
    }
    /**
     * @param int $dateTime
     * @return Slot[]
     * @throws \Exception
     */
    public function findSlotsByMonth(int $dateTime):array
    {
        return Slot::find()
            ->andWhere([ '>=', 'date',DateHelper::beginMonthDayByUnixTime($dateTime)])
            ->andWhere([ '<=', 'date',DateHelper::lastMonthDayByUnixTime($dateTime)])
            ->orderBy('date')
            ->all();
    }
    /**
     * @param int $dateTime
     * @return Slot[]
     * @throws \Exception
     */
    public function findSlotsByWeek(int $dateTime):array
    {
        return Slot::find()
            ->andWhere([ '>=', 'date',DateHelper::beginWeekDayByUnixTime($dateTime)])
            ->andWhere([ '<=', 'date',DateHelper::lastWeekDayByUnixTime($dateTime)])
            ->orderBy('date')
            ->all();
    }
    public function findAll():array
    {
        return $this->findAllBy([]);
    }
###

    private static function getBy(array $condition): Slot
    {
        if (!$entity = Slot::find()->andWhere($condition)->limit(1)->one()) {
            throw new NotFoundException('Слот не найден');
        }
        return $entity;
    }




    private function findAllBy(array $condition):array
    {
        return Slot::find()->andWhere($condition)->all();
    }

    private function findOneBy(array $condition):?Slot
    {
        return Slot::find()->andWhere($condition)->one();
    }
}