<?php

namespace booking\useCases\auth;

use booking\entities\User\User;
use booking\forms\auth\LoginForm;
use booking\repositories\UserRepository;

class AuthService
{
    private UserRepository $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    public function auth(LoginForm $form): User
    {
        $user = $this->users->findByEmail($form->email);
        if (!$user || !$user->isActive() || !$user->validatePassword($form->password)) {
            throw new \DomainException('Неверный email или пароль.');
        }
        return $user;
    }
}