<?php

namespace booking\forms\manage\User;

use booking\access\Rbac;
use booking\entities\User\User;
use yii\helpers\ArrayHelper;
use Yii;

class UserEditForm extends UserCreateForm
{
    public $email;
    public $name;
    public $surname;
    public $patronymic;
    public $telephone;
    public $roles;
    public  $status='';
    public ?string $gender='';
    public $_user;

    public function __construct(User $user, $config = [])
    {
        $this->email = $user->email;
        $this->name = $user->name;
        $this->surname = $user->surname;
        $this->patronymic = $user->patronymic;
        $this->telephone = $user->telephone;
        $this->status = $user->status;
        $this->gender = $user->gender;
        $this->_user = $user;

        $roles = Yii::$app->authManager->getRolesByUser($user->id);
        $this->roles = ArrayHelper::map($roles, 'name', 'name');
//        $this->role = $roles ? reset($roles)->name : null;
//        $this->roles = $roles;

        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['name', 'trim'],
            ['name', 'required'],
            ['name', 'string', 'min' => 2, 'max' => 255],
            ['surname', 'string', 'min' => 2, 'max' => 255],
            ['patronymic', 'string', 'min' => 2, 'max' => 255],
            ['telephone', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\booking\entities\User\User', 'filter' => function ($query) {
                    $query->andWhere(['not', ['id'=>$this->_user->id]]);
                },
                'message' => 'Email уже используется'],

            ['roles', 'required'],
            ['roles','default','value'=>Rbac::ROLE_USER],
//            [['roles'], 'in', 'range' => ArrayHelper::map(\Yii::$app->authManager->getRoles(), 'name', 'name')],

            [['gender'], 'in', 'range' => array_keys(User::getGenderList())],

            [['status'], 'in', 'range' => array_keys(User::getStatusList())],

        ];
    }
    public function attributeLabels():array
    {
        return User::getAttributeLabels();
    }
}