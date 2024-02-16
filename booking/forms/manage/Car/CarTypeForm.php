<?php

namespace booking\forms\manage\Car;

use booking\entities\Car\CarType;
use booking\entities\Slot\Slot;
use yii\base\Model;

class CarTypeForm extends Model
{
    public ?string $name=null;
    public ?string $description=null;
    public ?string $note=null;
    public ?int $status=null;
    public ?int $qty=null;
    public ?float $pwr=null;
    public ?int $type=null;
    public ?CarType $_carType;
    public function __construct(CarType $license=null, $config = [])
    {
        parent::__construct($config);
        if ($license) {
            $this->name=$license->name;
            $this->description=$license->description;
            $this->note=$license->note;
            $this->status=$license->status;
            $this->qty=$license->qty;
            $this->pwr=$license->pwr;
            $this->type=$license->type;

            $this->_carType = $license;
        } else {
            $this->status=CarType::STATUS_ACTIVE;
            $this->type=Slot::TYPE_ADULT;
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
            [['type'], 'in', 'range' => array_keys(Slot::getTypeList())],
            [['name','qty','description','status'],'required']
        ];
    }

    public function attributeLabels():array
    {
        return CarType::getAttributeLabels();
    }

}