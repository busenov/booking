<?php

namespace booking\useCases\manage;

use booking\entities\Slot\Slot;
use booking\forms\manage\Slot\SlotForm;
use booking\repositories\SlotRepository;

class SlotManageService
{
    private SlotRepository $repository;

    public function __construct(
        SlotRepository $repository
    )
    {
        $this->repository = $repository;
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
     * @param Slot|int $entityOrId
     * @param bool $return
     * @return bool
     */
    public static function guardCanEdit($entityOrId, bool $return=false):bool
    {
       return true;
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
        return true;
    }



### private

}