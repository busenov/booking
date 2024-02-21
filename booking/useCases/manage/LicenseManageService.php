<?php

namespace booking\useCases\manage;

use booking\entities\License\License;
use booking\entities\User\User;
use booking\forms\manage\User\LicenseForm;
use booking\repositories\LicenseRepository;
use booking\repositories\UserRepository;
use booking\services\TransactionManager;

class LicenseManageService
{
    private LicenseRepository $repository;
    private UserRepository $userRepository;
    private UserManageService $userService;
    private TransactionManager $transaction;

    public function __construct(
        LicenseRepository $repository,
        UserRepository $userRepository,
        UserManageService $userService,
        TransactionManager $transaction
    )
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->userService = $userService;
        $this->transaction = $transaction;
    }

    public function create(LicenseForm $form): License
    {
        $this->guardCanCreate();
        if (!$user=$this->userRepository->findByTelephone($form->telephone,true)) {
            $user=User::createCustomer($form->name,$form->telephone,null,$form->surname);
        }
        if ($form->name) {
            $user->name=$form->name;
        }
        if ($form->surname) {
            $user->surname = $form->surname;
        }
        $this->userRepository->save($user);
        $entity = License::create(
            (int)$form->number,
            $user->id,
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
        $user=$this->userRepository->getByTelephone($form->telephone,true);
        if ($form->name) {
            $user->name=$form->name;
        }
        if ($form->surname) {
            $user->surname = $form->surname;
        }
        $entity->edit(
            (int)$form->number,
            $user->id,
            (int)$form->status,
            (int)$form->date,
            $form->note
        );

        $this->transaction->wrap(function ()use ($entity,$user){
            $this->repository->save($entity);
            $this->userRepository->save($user);
        });

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