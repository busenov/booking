<?php

namespace booking\forms\manage\Car;

use booking\entities\Car\CarType;
use booking\entities\Car\Price;
use booking\entities\Slot\Slot;
use yii\base\Model;

class PriceForm extends Model
{
    public ?int $car_type_id=null;
    public ?string $weekday=null;
    public ?string $date_from=null;
    public ?string $status=null;
    public ?string $cost=null;
    public ?string $note=null;
    public ?Price $_price=null;
    public function __construct(Price $price=null, $config = [])
    {
        parent::__construct($config);
        if ($price) {
            $this->car_type_id=$price->car_type_id;
            $this->weekday=$price->weekday;
            $this->date_from=$price->date_from;
            $this->status=$price->status;
            $this->cost=$price->cost;
            $this->note=$price->note;

            $this->_price = $price;
        } else {
            $this->status=Price::STATUS_ACTIVE;
        }

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['car_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => CarType::class, 'targetAttribute' => ['number' => 'number']],
            [['cost',], 'double','min'=>0.1],
            [['weekday','date_from','status',], 'integer'],
            [['note'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => array_keys(Price::getStatusList())],
            [['weekday'], 'in', 'range' => array_keys(Price::getWeekdayList())],
            [['car_type_id','cost','status'],'required']
        ];
    }

    public function attributeLabels():array
    {
        return Price::getAttributeLabels();
    }

}