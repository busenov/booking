<?php
namespace console\controllers;

use booking\access\Rbac;
use booking\entities\User\User;
use booking\forms\manage\User\UserCreateForm;
use booking\helpers\AppHelper;
use booking\useCases\manage\UserManageService;
use Yii;
use yii\console\Controller;
use yii\console\Exception;
use yii\helpers\ArrayHelper;

/**
 * Управление пользователями (User manage)
 */
class UserController extends Controller
{
    private UserManageService $service;

    public function __construct($id, $module, UserManageService $slotService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $slotService;
    }


    /**
     * Добавление нового супер админа(Add user)
     * @return void
     */
    public function actionCreateSuperAdmin():void
    {
        if (AppHelper::isDevelop()) {
            $email = $this->prompt('Email:', [
                'required' => true,
            ]);
            $password = $this->prompt('Password:', [
                'required' => true,
                'validator' => function ($input, &$error) {
                    if (strlen($input) < User::getPasswordMinimum()) {
                        $error = 'The password must be exactly '.User::getPasswordMinimum().' chars!';
                        return false;
                    }
                    return true;
                },
            ]);
            $form=new UserCreateForm([
                'name' => 'Super Admin',
                'email'=>$email,
                'password'=>$password,
                'status'=> User::STATUS_ACTIVE,
                'roles' => [Rbac::ROLE_SUPER_ADMIN=>Rbac::ROLE_SUPER_ADMIN]
            ]);
            $user=$this->service->create($form);

            $this->stdout('User success created!' . PHP_EOL);
        } else {
            $this->stdout('On production forbidden!' . PHP_EOL);
        }
    }

    /**
     * Добавление нового пользователя (Add user)
     * @return void
     */
    public function actionCreate():void
    {
        $email = $this->prompt('Email:', [
            'required' => true,
        ]);
        $password = $this->prompt('Password:', [
            'required' => true,
            'validator' => function ($input, &$error) {
                if (strlen($input) < User::getPasswordMinimum()) {
                    $error = 'The password must be exactly '.User::getPasswordMinimum().' chars!';
                    return false;
                }
                return true;
            },
        ]);
        $form=new UserCreateForm([
            'name' => 'Новый пользователь',
            'email'=>$email,
            'password'=>$password,
            'role' => Rbac::ROLE_USER
        ]);
        $user=$this->service->create($form);

        $this->stdout('User success created!' . PHP_EOL);
    }
    /**
     * Adds role to user
     */
    public function actionAssign(): void
    {
        $email = $this->prompt('Email:', ['required' => true]);
        $user = $this->findModel($email);
        $role = $this->select('Role:', ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'description'));
        $this->service->assignRole($user->id, $role);
        $this->stdout('Done!' . PHP_EOL);
    }
###
    private function findModel($email): User
    {
        if (!$model = User::findOne(['email' => $email])) {
            throw new Exception('User is not found');
        }
        return $model;
    }
}