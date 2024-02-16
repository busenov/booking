<?php

namespace booking\forms\manage\User;

use booking\access\Rbac;
use booking\entities\User\User;
use booking\forms\CompositeForm;
use booking\forms\manage\License\LicenseForm;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * @property int $slot_id
 * @property int $customer_id
 * @property int $status
 * @property int $isChild
 * @property string $note
 *
 * @property LicenseForm $license
 */
class RacerForm extends CompositeForm
{
    public ?string $email;
    public ?string $name;
    public ?string $surname;
    public ?string $patronymic;
    public ?string $telephone;
    public ?User $_user;

    public function __construct(?User $user=null, $config = [])
    {
        parent::__construct($config);
        $this->license=new LicenseForm();
        if ($user) {
            $this->email=$user->email;
            $this->name=$user->name;
            $this->surname=$user->surname;
            $this->patronymic=$user->patronymic;
            $this->telephone=$user->telephone;

            $this->_user=$user;
        }

    }

    public function rules(): array
    {
        return [
            ['name', 'trim'],
            ['name', 'required'],
            ['name', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
//            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\booking\entities\User\User', 'message' => 'Email уже используется'],

            ['surname', 'string', 'min' => 2, 'max' => 255],
            ['patronymic', 'string', 'min' => 2, 'max' => 255],
            ['telephone', 'string', 'min' => 2, 'max' => 255],
            ['telephone', 'required'],

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

    protected function internalForms(): array
    {
        return ['license'];
    }
}