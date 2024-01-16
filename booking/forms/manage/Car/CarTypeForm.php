<?php

namespace booking\forms\manage\Car;

use booking\entities\Car\CarType;
use yii\base\Model;

class CarTypeForm extends Model
{
    public ?string $name=null;
    public ?string $description=null;
    public ?string $note=null;
    public ?int $status=null;
    public ?int $qty=null;
    public ?float $pwr=null;
    public ?CarType $_carType;
    public function __construct(CarType $slot=null, $config = [])
    {
        parent::__construct($config);
        if ($slot) {
            $this->name=$slot->name;
            $this->description=$slot->description;
            $this->note=$slot->note;
            $this->status=$slot->status;
            $this->qty=$slot->qty;
            $this->pwr=$slot->pwr;

            $this->_carType = $slot;
        } else {
            $this->status=CarType::STATUS_ACTIVE;
        }

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qty',], 'integer', 'min'=>1],
            [['pwr',], 'double','min'=>0.1],
            [['name', 'note'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 1024],
            [['status'], 'in', 'range' => array_keys(CarType::getStatusList())],
            [['name','qty','description','status'],'required']
        ];
    }

    public function attributeLabels():array
    {
        return CarType::getAttributeLabels();
    }

}