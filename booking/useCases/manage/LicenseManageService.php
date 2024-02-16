<?php

namespace booking\useCases\manage;

use booking\entities\License\License;
use booking\forms\manage\License\LicenseForm;
use booking\repositories\LicenseRepository;

class LicenseManageService
{
    private LicenseRepository $repository;

    public function __construct(
        LicenseRepository $repository
    )
    {
        $this->repository = $repository;
    }

    public function create(LicenseForm $form): License
    {
        $this->guardCanCreate();
        $entity = License::create(
            (int)$form->number,
            (int)$form->user_id,
            (int)$form->status,
            (int)$form->date,
            $form->note
        );

        $this->repository->save($entity);

        return $entity;
    }

    public function edit($entityOrId, LicenseForm $form):void
    {
        $entity=$this->repository->get($entityOrId);
        $this->guardCanEdit($entity);
        $entity->edit(
            (int)$form->number,
            (int)$form->user_id,
            (int)$form->status,
            (int)$form->date,
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
     * @param License|int $entityOrId
     * @param bool $return
     * @return bool
     */
    public static function guardCanEdit($entityOrId, bool $return=false):bool
    {
       return true;
    }

    /**
     * Можно удалять
     * @param License|int $entityOrId
     * @param bool $return
     * @return bool
     */
    public static function guardCanRemove($entityOrId, bool $return=false):bool
    {
        return true;
    }
    /**
     * Можно жестко удалять
     * @param License|int $entityOrId
     * @param bool $return
     * @return bool
     */
    public static function guardCanRemoveHard($entityOrId, bool $return=false):bool
    {
        return true;
    }



### private

}