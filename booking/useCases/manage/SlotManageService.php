<?php

namespace booking\useCases\manage;

use booking\entities\Schedule\Schedule;
use booking\entities\Slot\Slot;
use booking\forms\manage\Slot\SlotForm;
use booking\helpers\DateHelper;
use booking\repositories\CarTypeRepository;
use booking\repositories\ScheduleRepository;
use booking\repositories\SlotRepository;
use http\Exception\RuntimeException;

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
    public function clear(?int $unixTime=null)
    {
        $this->guardCanClear();
        if ($unixTime) {
            $entities=$this->repository->findSlotsByDay($unixTime);
        } else {
            $entities=$this->repository->findAll();
        }

        foreach ($entities as $entity) {
            if ($entity->hasReserved()) {
                throw new \DomainException('Нельзя очистить слоты имеются брони на этот день: '.date('d.m.Y',$entity->date));
            } else {
                $this->removeHard($entity);
            }
        }
    }
    /**
     * Генерация слотов по расписанию.
     * @param int|null $unixTime // с какого дня начать. По умолчанию начало текущего
     * @param int|null $plusDay       // на сколько дней вперед
     * @return void
     */
    public function generateSlots(?int $unixTime=null, ?int $plusDay=null):void
    {
        $dateTime=DateHelper::beginDay($unixTime?:time());
        /**
         * Находим нужное расписание
         */
        $schedule=$this->scheduleRepository->findByWeekday(date('N',$dateTime));
        $lastTime=$schedule->begin;
        $endSchedule=$schedule->end;
        if (empty($schedule)) {
            throw new \RuntimeException('Нет расписание для '.date('l',$dateTime));
        }
        /**
         * Проверяем есть ли слоты на этот день. Если слоты есть, берем последний слот и добиваем этот день
         */
        if ($slots=$this->repository->findSlotsByDay($dateTime)) {
            $lastSlot=end($slots);
            $lastTime=$lastSlot->end + $schedule->interval;
        }

        /**
         * Генерируем слоты
         */
        if ($countCars=$this->carTypeRepository->sumActiveCar()) {
            while ($lastTime < $endSchedule) {
                $slot=Slot::create(
                    $dateTime,
                    $lastTime,
                    $lastTime+$schedule->duration,
                    $countCars
                );
                $this->repository->save($slot);
                $lastTime=$lastTime + $schedule->duration + $schedule->interval;
            }
        }
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


### private



}