<?php

namespace booking\forms\manage\User;

use booking\entities\User\User;
use booking\repositories\UserRepository;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class AssignUserForm extends Model
{
    public $userId='';
    public $withOutUsers=null;
    private UserRepository $userRepository;

    /**
     * @param array $withOutUsers       - массив id, исключить который из списка пользователей
     * @param $config
     */
    public function __construct(array $withOutUsers=[], $config = [])
    {
        $this->withOutUsers=$withOutUsers;
        $this->userRepository = new UserRepository();
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['userId'], 'integer'],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['userId' => 'id']],
        ];
    }
    public function attributeLabels():array
    {
        return [
            'userId'=>'Пользователь'
        ];
    }

    public function userList():array
    {
        return ArrayHelper::map($this->userRepository->findActiveWithoutUsers($this->withOutUsers), 'id', 'shortName');
    }
    public function administratorList():array
    {
        return ArrayHelper::map($this->userRepository->findAdmins(), 'id', 'shortName');
    }
}