<?php

namespace booking\forms\manage\Car;

use booking\entities\Car\CarType;
use booking\entities\Slot\Slot;
use booking\forms\CompositeForm;
use yii\base\Model;

/**
 * @property PriceForm[] $prices
 */
class CarTypeForm extends CompositeForm
{
    public ?string $name=null;
    public ?string $description=null;
    public ?string $note=null;
    public ?int $status=null;
    public ?int $qty=null;
    public ?float $pwr=null;
    public ?int $type=null;
    public ?CarType $_carType;
    public function __construct(CarType $carType=null, $config = [])
    {
        parent::__construct($config);
        $prices=[];
        if ($carType) {
            $this->name=$carType->name;
            $this->description=$carType->description;
            $this->note=$carType->note;
            $this->status=$carType->status;
            $this->qty=$carType->qty;
            $this->pwr=$carType->pwr;
            $this->type=$carType->type;

            foreach ($carType->prices as $price) {
                $prices[]=new PriceForm($price);
            }
            $this->_carType = $carType;
        } else {
            $this->status=CarType::STATUS_ACTIVE;
            $this->type=Slot::TYPE_ADULT;
        }
        if (empty($prices)) {
            $prices[]=new PriceForm();
        }
        $this->prices=$prices;
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

    protected function internalForms(): array
    {
        return ['prices'];
    }
}