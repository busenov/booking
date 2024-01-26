<?php

namespace booking\useCases\manage;

use booking\entities\Schedule\Schedule;
use booking\entities\Slot\Slot;
use booking\forms\manage\Slot\GenerateForm;
use booking\forms\manage\Slot\SlotForm;
use booking\helpers\DateHelper;
use booking\repositories\CarTypeRepository;
use booking\repositories\ScheduleRepository;
use booking\repositories\SlotRepository;
use http\Exception\RuntimeException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class SlotManageService
{
    private SlotRepository $repository;
    private ScheduleRepository $scheduleRepository;
    private CarTypeRepository $carTypeRepository;

    public function __construct(
        SlotRepository $repository,
        ScheduleRepository $scheduleRepository,
        CarTypeRepository $carTypeRepository
    )
    {
        $this->repository = $repository;
        $this->scheduleRepository = $scheduleRepository;
        $this->carTypeRepository = $carTypeRepository;
    }

    public function create(SlotForm $form): Slot
    {
        $this->guardCanCreate();
        $entity = Slot::create(
            (int)$form->date,
            (int)$form->begin,
            (int)$form->end,
            (int)$form->qty,
            (int)$form->status,
            $form->note
        );

        $this->repository->save($entity);

        return $entity;
    }

    public function edit($entityOrId, SlotForm $form):void
    {
        $entity=$this->repository->get($entityOrId);
        $this->guardCanEdit($entity);
        $entity->edit(
            (int)$form->date,
            (int)$form->begin,
            (int)$form->end,
            (int)$form->qty,
            (int)$form->status,
            (bool)$form->is_child,
            $form->note
        );
        $this->repository->save($entity);
    }

    public function remove($entityOrId): void
    {
        $entity = $this->repository->get($entityOrId);
        $this->guardCanRemove($entity);
        $entity->onDeleted();
        $this->repository->save($entity);
    }
    public function removeHard($entityOrId): void
    {
        $entity = $this->repository->get($entityOrId);
        $this->guardCanRemoveHard($entity);
        $entity->delete();
    }
    public function clear(?int $unixTime=null,bool $force=false)
    {
        $this->guardCanClear();
        if ($unixTime) {
            $entities=$this->repository->findSlotsByDay($unixTime);
        } else {
            $entities=$this->repository->findAll();
        }

        foreach ($entities as $entity) {
            if ((!$force)and($entity->hasReserved())) {
                throw new \DomainException(
                    'Нельзя очистить заезды на этот день. 
                    Имеются брони заезда: '. $entity->getName() .'. 
                    Все равно очистить(брони связанные с этим заездом тоже будут удалены)? <a href="'.Url::to(['clear-day','unixTime'=>$entity->date,'force'=>true]).' " class="btn btn-danger btn-sm">Принудительно</a>');
            } else {
                if ($force) {
                    $entity->orders=[];
                    $this->repository->save($entity);
                }
                $this->removeHard($entity);
            }
        }
    }

    /**
     * Создание слотов на определенный день
     * @param int $unixTime
     * @param GenerateForm $form
     * @return int
     */
    public function generateSlotsForDay(int $unixTime, GenerateForm $form):int
    {
        $dateTime=DateHelper::beginDay($unixTime?:time());

        $lastTime=$form->begin;
        $endSchedule=$form->end;
        if (empty($form)) {
            throw new \RuntimeException('Нет расписание для '.date('l',$dateTime));
        }
        /**
         * Проверяем есть ли слоты на этот день. Если слоты есть, берем последний слот и добиваем этот день
         */
        if ($slots=$this->repository->findSlotsByDay($dateTime)) {
            $lastSlot=end($slots);
            $lastTime=$lastSlot->end + $form->interval;
        }

        /**
         * Генерируем слоты
         * Кол-во мест в заезде не может быть больше slot.qtyMax и не больше активных машин
         */
        $countCars=$this->carTypeRepository->sumActiveCar();
        $qty=min(Slot::getMaxQty(),$countCars);
        $countSlots=0;
        while ($lastTime+$form->duration < $endSchedule) {
            $slot=Slot::create(
                $dateTime,
                $lastTime,
                $lastTime+$form->duration,
                $qty,
                $form->activateSlot?Slot::STATUS_ACTIVE:Slot::STATUS_NEW
            );
            $this->repository->save($slot);
            $lastTime=$lastTime + $form->duration + $form->interval;
            $countSlots++;
        }
        return $countSlots;
    }
    /**
     * Генерация слотов по расписанию.
     * @param int|null $unixTime        // с какого дня начать. По умолчанию начало текущего
     * @param int|null $plusDay         // на сколько дней вперед
     * @return int
     */
    public function generateSlots(?int $unixTime=null, ?int $plusDay=0):int
    {
        $this->guardCanGenerateSlots();
        $dateTime=DateHelper::beginDay($unixTime?:time());
        $count=0;
        if ($plusDay) {
            for ($i=0;$i<=$plusDay;$i++){
                $dateTime=DateHelper::beginDay($dateTime+(60*60*24));
                /**
                 * Находим нужное расписание
                 */
                $schedule=$this->scheduleRepository->findByWeekday(date('N',$dateTime));
                $count+=$this->generateSlotsForDay($dateTime,new GenerateForm($schedule));
            }
        }
        return $count;
    }
    public function changeStatusByDay(int $unixTime,int $status):int
    {
        $entities=$this->repository->findSlotsByDay($unixTime);
        $qty=0;
        if (!ArrayHelper::getValue(Slot::getStatusList(), $status)) {
            throw new RuntimeException('Не верный статус');
        }
        foreach ($entities as $entity) {
            $this->guardCanChangeStatus($entity);
            $entity->status=$status;
            $this->repository->save($entity);
            $qty++;
        }
        return $qty;
    }
###guards
    public static function guardCanView($entityOrId, bool $return=false):bool
    {
        return true;
    }
    /**
     * Можно создавать
     * @return bool
     */
    public static function guardCanCreate(bool $return=false):bool
    {
        return \Yii::$app->user->can('admin');
    }

    /**
     * Можно редактировать
     *
     * @param Slot|int $entityOrId
     * @param bool $return
     * @return bool
     */
    public static function guardCanEdit($entityOrId, bool $return=false):bool
    {
        return \Yii::$app->user->can('admin');
    }

    /**
     * Можно удалять
     * @param Slot|int $entityOrId
     * @param bool $return
     * @return bool
     */
    public static function guardCanRemove($entityOrId, bool $return=false):bool
    {
        return true;
    }
    /**
     * Можно жестко удалять
     * @param Slot|int $entityOrId
     * @param bool $return
     * @return bool
     */
    public static function guardCanRemoveHard($entityOrId, bool $return=false):bool
    {
        return \Yii::$app->user->can('admin');
    }
    public static function guardCanClear(bool $return=false):bool
    {
        return \Yii::$app->user->can('admin');
    }
    public static function guardCanGenerateSlots(bool $return=false):bool
    {
        return \Yii::$app->user->can('admin');
    }
    /**
     * Можно ли менять статус.
     * Условия:
     * - нельзя если уже есть бронь оп этом заезду
     * - можно администратору
     *
     * @param Slot|int $entityOrId
     * @param bool $return
     * @return bool
     */
    public static function guardCanChangeStatus($entityOrId, bool $return=false):bool
    {
        if (!($entity=static::getSlot($entityOrId,$return))) return false;

        if (\Yii::$app->user->can('admin')) {
            if ($entity->hasOrders()) {
                if ($return) return false;
                throw new \RuntimeException('Запрещено менять статусы у заездов с заказами');
            } else {
                return true;
            }
        } else {
            if ($return) return false;
            throw new \RuntimeException('Разрешено менять статусы только Администраторам');
        }
    }


### private

    /**
     * @param Slot|int $entityOrId
     * @param bool $return
     * @return Slot|null
     */
    private static function getSlot($entityOrId, bool $return=false):?Slot
    {
        if ($entity=SlotRepository::find_st($entityOrId)) {
            return $entity;
        }
        if ($return) return null;

        throw new \DomainException('Не найден заезд');
    }
}