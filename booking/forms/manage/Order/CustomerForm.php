<?php

namespace booking\forms\manage\Order;

use booking\entities\Car\CarType;
use booking\entities\User\User;
use yii\base\Model;

class CustomerForm extends Model
{
    public ?string $name=null;
    public ?string $surname=null;
    public ?string $telephone=null;
    public ?string $email=null;
    public ?User $_customer;
    public function __construct(User $customer=null, $config = [])
    {
        parent::__construct($config);
        if ($customer) {
            $this->name=$customer->name;
            $this->telephone=$customer->telephone;

            $this->_customer = $customer;
        }

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name','surname'], 'string', 'max' => 255],
            [['email'], 'email'],
            ['telephone', 'string', 'min' => 2, 'max' => 255],
            [['name','telephone'],'required']
        ];
    }

    public function attributeLabels():array
    {
        return User::getAttributeLabels();
    }

}