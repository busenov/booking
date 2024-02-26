<?php

namespace booking\useCases\auth;

use booking\access\Rbac;
use booking\entities\User\User;
use booking\forms\auth\AdminSignupForm;
use booking\forms\auth\SignupForm;
use booking\repositories\UserRepository;
use booking\services\RoleManager;
use booking\services\TransactionManager;
use yii\mail\MailerInterface;

class SignupService
{
    private MailerInterface $mailer;
    private UserRepository $users;
    private TransactionManager $transaction;
    private RoleManager $roles;

    public function __construct(
        UserRepository $users,
        MailerInterface $mailer,
        TransactionManager $transaction,
        RoleManager $roles
    )
    {
        $this->mailer = $mailer;
        $this->users = $users;
        $this->transaction = $transaction;
        $this->roles = $roles;
    }

    public function signup(SignupForm $form): void
    {
        $user = User::requestSignup(
            $form->name,
            $form->surname,
            $form->email,
            $form->password
        );
        $this->transaction->wrap(function () use ($user, $form) {
            $this->users->save($user);
            $this->roles->assign($user->id, Rbac::ROLE_USER);
        });

        $sent = $this->mailer
            ->compose(
                ['html' => 'auth/signup/confirm-html', 'text' => 'auth/signup/confirm-text'],
                ['user' => $user]
            )
            ->setTo($form->email)
            ->setSubject('Подтверждение регистрации на сайте ' . \Yii::$app->name)
            ->send();
        if (!$sent) {
            throw new \RuntimeException('Email sending error.');
        }
    }

    public function adminSignup(AdminSignupForm $form): void
    {
        if ($user = $this->users->findByEmail($form->email)) {
            throw new \DomainException('Пользователь с email: '. $form->email . ' уже зарегистрирован');
        }
        $user= User::requestSignup($form->name,'',$form->email,$form->password);


        $this->transaction->wrap(function () use ($user, $form) {

            //сохраняем пользователя
            $this->users->save($user);
            //добавляем роль админа
            $this->roles->assign($user->id, Rbac::ROLE_USER);
            $this->users->save($user);
        });

        $sent = $this->mailer
            ->compose(
                ['html' => 'auth/signup/confirmAdmin-html', 'text' => 'auth/signup/confirmAdmin-text'],
                ['user' => $user]
            )
            ->setTo($form->email)
            ->setSubject('Подтверждение регистрации на сайте ' . \Yii::$app->name)
            ->send();

        if (!$sent) {
            throw new \RuntimeException('Email sending error.');
        }
    }

    public function confirm($token): void
    {
        if (empty($token)) {
            throw new \DomainException('Empty confirm token.');
        }
        $user = $this->users->getByEmailConfirmToken($token);
        $user->confirmSignup();
        $this->users->save($user);
    }
}