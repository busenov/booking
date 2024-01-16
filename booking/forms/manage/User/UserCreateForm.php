<?php

namespace booking\forms\manage\User;

use booking\access\Rbac;
use booking\entities\User\User;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class UserCreateForm extends Model
{
    public $email;
    public $password;
    public $name;
    public $surname;
    public $patronymic;
    public $telephone;
    public $roles;
    public $status=User::STATUS_WAIT;
    public ?string $gender=null;

    public function __construct($config = [])
    {
        parent::__construct($config);
//        $this->status=
    }

    public function rules(): array
    {
        return [
            ['name', 'trim'],
            ['name', 'required'],
            ['name', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\booking\entities\User\User', 'message' => 'Email уже используется'],

            ['surname', 'string', 'min' => 2, 'max' => 255],
            ['patronymic', 'string', 'min' => 2, 'max' => 255],
            ['telephone', 'string', 'min' => 2, 'max' => 255],

            ['password', 'required'],
            ['password', 'string', 'min' => User::getPasswordMinimum()],

            ['roles', 'required'],
            ['roles','default','value'=>Rbac::ROLE_USER],
//            [['roles'], 'in', 'range' => ArrayHelper::map(\Yii::$app->authManager->getRoles(), 'name', 'name')],

            [['gender'], 'in', 'range' => array_keys(User::getGenderList())],

            ['status','default','value'=>User::STATUS_WAIT],

        ];
    }

    public function beforeValidate(): bool
    {
        if (parent::beforeValidate()) {
            return true;
        }
        return false;
    }



    public function attributeLabels():array
    {
        return User::getAttributeLabels();
    }
}