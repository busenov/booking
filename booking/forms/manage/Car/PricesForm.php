<?php

namespace booking\forms\manage\Car;

use booking\entities\Car\CarType;
use booking\entities\Slot\Slot;
use booking\forms\CompositeForm;
use yii\base\Model;

/**
 * @property PriceForm[] $prices
 */
class PricesForm extends CompositeForm
{
    public ?CarType $_carType;
    public $safe;
    public function __construct(CarType $carType=null, $config = [])
    {
        parent::__construct($config);
        $prices=[];
        if ($carType) {
            foreach ($carType->prices as $price) {
                $prices[]=new PriceForm($price);
            }
            $this->_carType = $carType;
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
            [['safe',], 'safe'],
        ];
    }


    protected function internalForms(): array
    {
        return ['prices'];
    }
}