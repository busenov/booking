<?php

namespace booking\useCases\manage;

use booking\entities\Car\CarType;
use booking\entities\Schedule\Schedule;
use booking\forms\manage\Car\CarTypeForm;
use booking\forms\manage\Schedule\ScheduleForm;
use booking\repositories\CarTypeRepository;
use booking\repositories\ScheduleRepository;
use booking\repositories\UserRepository;
use booking\services\TransactionManager;

class ScheduleManageService
{
    private ScheduleRepository $repository;

    public function __construct(
        ScheduleRepository $repository
    )
    {
        $this->repository = $repository;
    }

    public function create(ScheduleForm $form): int
    {
        $this->guardCanCreate();
        $count=0;
        if ($form->weekday > 0) {
            if (!$this->repository->findByWeekday($form->weekday)) {
                $entity = Schedule::create(
                    (int)$form->begin,
                    (int)$form->end,
                    (int)$form->duration_min*60,
                    (int)$form->interval_min*60,
                    (int)$form->sort,
                    (int)$form->weekday,
                    (int)$form->status,
                    $form->note
                );

                $this->repository->save($entity);
                $count++;
            } else {
                throw new \RuntimeException('Расписание на '. Schedule::weekdayName($form->weekday).' уже существует');
            }
        } else {
            $weekdays=[];

            if ($form->weekday==Schedule::WEEKDAY_WORK) {
                $weekdays=Schedule::getWeekdaysWork();
            } else if ($form->weekday==Schedule::WEEKDAY_WEEKEND) {
                $weekdays=Schedule::getWeekdaysWeekend();
            }
            foreach ($weekdays as $weekday) {
                $form->weekday=$weekday;
                $form->sort = Schedule::find()->max('sort') + 1;
//                try
                $count+=self::create($form);
            }

        }
        return $count;
    }

    public function edit($entityOrId, ScheduleForm $form):void
    {
        $entity=$this->repository->get($entityOrId);
        $this->guardCanEdit($entity);
        $entity->edit(
            (int)$form->begin,
            (int)$form->end,
            (int)$form->duration_min,
            (int)$form->interval_min,
            (int)$form->sort,
            (int)$form->weekday,
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
    public function clear()
    {
        $this->guardCanClear();
        $entities=$this->repository->findAll();
        foreach ($entities as $entity) {
            $this->removeHard($entity);
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
        return true;
    }

    /**
     * Можно редактировать
     *
     * @param Schedule|int $entityOrId
     * @param bool $return
     * @return bool
     */
    public static function guardCanEdit($entityOrId, bool $return=false):bool
    {
       return true;
    }

    /**
     * Можно удалять
     * @param Schedule|int $entityOrId
     * @param bool $return
     * @return bool
     */
    public static function guardCanRemove($entityOrId, bool $return=false):bool
    {
        return true;
    }
    /**
     * Можно жестко удалять
     * @param Schedule|int $entityOrId
     * @param bool $return
     * @return bool
     */
    public static function guardCanRemoveHard($entityOrId, bool $return=false):bool
    {
        return true;
    }
    public static function guardCanClear(bool $return=false):bool
    {
        return true;
    }



### private

}