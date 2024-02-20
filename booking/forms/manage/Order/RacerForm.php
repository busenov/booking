<?php

namespace booking\forms\manage\Order;

use booking\entities\Car\CarType;
use booking\entities\Slot\Slot;
use booking\entities\User\User;
use yii\base\Model;

class RacerForm extends Model
{
    public ?string $name=null;
    public ?string $weight=null;
    public ?string $height=null;
    public ?string $birthday=null;
    public ?string $slot_id=null;
    public ?Slot $slot=null;
    public function __construct(Slot $slot, $config = [])
    {
        $this->slot_id=$slot->id;
        $this->slot=$slot;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
            [['weight','height'], 'double', 'min' => 0],
            [['birthday','slot_id'], 'integer'],
        ];
    }

    public function attributeLabels():array
    {
        return [
            'name'=>'Имя',
            'weight'=>'Вес',
            'height'=>'Рост',
            'birthday'=>'День рождения',
        ];
    }

}