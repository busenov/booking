<?php

namespace booking\forms\manage\Car;

use booking\entities\Car\CarType;

class CarTypeForm extends CarType
{
    public ?string $name=null;
    public ?string $description=null;
    public ?string $note=null;
    public ?int $status=null;
    public ?int $qty=null;
    public ?float $pwr=null;
    public ?CarType $_carType;
    public function __construct(CarType $carType=null, $config = [])
    {
        parent::__construct($config);
        if ($carType) {
            $this->name=$carType->name;
            $this->description=$carType->description;
            $this->note=$carType->note;
            $this->status=$carType->status;
            $this->qty=$carType->qty;
            $this->pwr=$carType->pwr;

            $this->_carType = $carType;
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
            [['qty',], 'integer', 'min'=>1,'default'=>1],
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