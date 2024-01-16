<?php

namespace booking\useCases\manage;

use booking\entities\Car\CarType;
use booking\forms\manage\Car\CarTypeForm;
use booking\repositories\CarTypeRepository;
use booking\repositories\UserRepository;
use booking\services\TransactionManager;

class CarTypeManageService
{
    private CarTypeRepository $repository;

    public function __construct(
        CarTypeRepository $repository,
    )
    {
        $this->repository = $repository;
    }

    public function create(CarTypeForm $form): CarType
    {
        $this->guardCanCreate();
        $entity = CarType::create(
            $form->name,
            $form->description,
            (int)$form->status,
            (int)$form->qty,
            (double)$form->pwr,
            $form->note
        );

        $this->repository->save($entity);

        return $entity;
    }

    public function edit($entityOrId, CarTypeForm $form):void
    {
        $entity=$this->repository->get($entityOrId);
        $this->guardCanEdit($entity);
        $entity->edit(
            $form->name,
            $form->description,
            (int)$form->status,
            (int)$form->qty,
            (double)$form->pwr,
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
     * @param CarType|int $entityOrId
     * @param bool $return
     * @return bool
     */
    public static function guardCanEdit($entityOrId, bool $return=false):bool
    {
       return true;
    }

    /**
     * Можно удалять
     * @param CarType|int $entityOrId
     * @param bool $return
     * @return bool
     */
    public static function guardCanRemove($entityOrId, bool $return=false):bool
    {
        return true;
    }
    /**
     * Можно жестко удалять
     * @param CarType|int $entityOrId
     * @param bool $return
     * @return bool
     */
    public static function guardCanRemoveHard($entityOrId, bool $return=false):bool
    {
        return true;
    }



### private

}