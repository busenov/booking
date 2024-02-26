<?php

namespace booking\useCases\manage;

use booking\entities\User\User;
use booking\forms\manage\User\UserCreateForm;
use booking\forms\manage\User\UserEditForm;
use booking\repositories\NotFoundException;
use booking\repositories\UserRepository;
use booking\services\RoleManager;
use booking\services\TransactionManager;

class UserManageService
{
    private UserRepository $repository;
    private RoleManager $roles;
    private TransactionManager $transaction;

    public function __construct(
        UserRepository $repository,
        RoleManager $roles,
        TransactionManager $transaction
    )
    {
        $this->repository = $repository;
        $this->roles = $roles;
        $this->transaction = $transaction;
    }

    public function create(UserCreateForm $form): User
    {
        $user = User::create(
            $form->name,
            $form->email,
            $form->telephone,
            $form->password,
            $form->surname,
            $form->patronymic,
            (int)$form->gender,
            (int)$form->status
        );

        $this->transaction->wrap(function () use ($user, $form) {
            $this->repository->save($user);
            foreach ($form->roles as $role) {
                $this->roles->assign($user->id, $role);
            }
        });
        return $user;
    }

    public function edit($id, UserEditForm $form): void
    {
        $user = $this->repository->get($id);
        $user->edit(
            $form->name,
            $form->email,
            $form->surname,
            $form->patronymic,
            $form->telephone,
            (int)$form->gender,
            (int)$form->status,
        );
        $this->transaction->wrap(function () use ($user, $form) {
            $this->repository->save($user);
            $this->roles->revokeAll($user->id);
            foreach ($form->roles as $role) {
                $this->roles->assign($user->id, $role);
            }
        });

    }

    public function assignRole($id, $role): void
    {
        $user = $this->repository->get($id);
        $this->roles->assign($user->id, $role);
    }

    public function remove($id): void
    {
        $user = $this->repository->get($id);

        $this->transaction->wrap(function () use ($user,$id){

            //удаляем пользователя
            $this->repository->remove($user);
            //очищаем права
            $authManager = \Yii::$app->get('authManager');
            $authManager->revokeAll($id);
        });

    }

    public function changePassword($id, $newPassword)
    {
        $user = $this->repository->get($id);
        $user->password=$newPassword;
        $this->repository->save($user);;
    }


    /**
     * Устанавливаем роль для пользователя
     */
    protected function setRole(int $id, array $roles)
    {
        if(!empty( $roles ))
        {
            /** @var \yii\rbac\DbManager $authManager */

            $authManager = \Yii::$app->authManager;
            $authManager->revokeAll($id);

            foreach ($roles as $item)
            {
                $r = $authManager->createRole($item);
                $authManager->assign($r,$id);
            }
        }
        else
        {
            throw new NotFoundException('Bad Request.');
        }

    }
}