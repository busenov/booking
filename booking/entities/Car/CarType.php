<?php

namespace booking\entities\Car;

use booking\entities\behaviors\FillingServiceFieldsBehavior;
use booking\entities\behaviors\LoggingBehavior;
use booking\entities\Order\Order;
use booking\entities\Slot\Slot;
use booking\repositories\OrderRepository;
use booking\repositories\SlotRepository;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "car_types".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $description
 * @property string|null $note
 * @property int|null $status
 * @property int $qty
 * @property double $pwr
 * @property int $type
 * @property int $amocrm_field_id
 *
 *
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $author_id
 * @property string|null $author_name
 * @property int|null $editor_id
 * @property string|null $editor_name
 *
 * @property Price[] $prices
 */
class CarType extends ActiveRecord
{
    const STATUS_ACTIVE=10;              //Активный
    const STATUS_INACTIVE=5;             //Не активный
    const STATUS_DELETED=100;            //Удален

    public static function create(
                                string  $name,
                                string  $description,
                                int     $status=self::STATUS_ACTIVE,
                                int     $qty = 1,
                                ?float  $pwr=null,
                                ?string $note=null,
                                ?int    $type=Slot::TYPE_ADULT,
                                ?int    $amocrm_field_id=null

                            ):self
    {
        return new self([
            'name'=>$name,
            'description'=>$description,
            'status'=>$status,
            'qty'=>$qty,
            'pwr'=>$pwr,
            'note'=>$note,
            'type'=>$type,
            'amocrm_field_id'=>$amocrm_field_id
        ]);
    }
    public function edit(
        string  $name,
        string  $description,
        int     $status=self::STATUS_ACTIVE,
        int     $qty = 1,
        ?float  $pwr=null,
        ?string $note=null,
        ?int    $type=Slot::TYPE_ADULT,
        ?int    $amocrm_field_id=null
    ):void
    {
        $this->name=$name;
        $this->description=$description;
        $this->status=$status;
        $this->qty=$qty;
        $this->pwr=$pwr;
        $this->note=$note;
        $this->type=$type;
        $this->amocrm_field_id=$amocrm_field_id;

    }
#on
    public function onActive()
    {
        $this->status=self::STATUS_ACTIVE;
    }
    public function onInactive()
    {
        $this->status=self::STATUS_INACTIVE;
    }
    public function onDeleted()
    {
        $this->status=self::STATUS_DELETED;
    }

    public function onAdult()
    {
        $this->type=Slot::TYPE_ADULT;
    }
    public function onChild()
    {
        $this->type=Slot::TYPE_CHILD;
    }
    public function onClub()
    {
        $this->type=Slot::TYPE_CLUB;
    }
#is
    public function isActive():bool
    {
        return $this->status===self::STATUS_ACTIVE;
    }
    public function isInactive():bool
    {
        return $this->status===self::STATUS_INACTIVE;
    }
    public function isDeleted():bool
    {
        return $this->status===self::STATUS_DELETED;
    }
    public function isAdult():bool
    {
        return $this->status===Slot::TYPE_ADULT;
    }
    public function isChild():bool
    {
        return $this->status===Slot::TYPE_CHILD;
    }
    public function isClub():bool
    {
        return $this->status===Slot::TYPE_CLUB;
    }
    public function isIdEqualTo($id):bool
    {
        return $this->id == $id;
    }
#gets
    public function getFreeBySlot(int $slotId=null)
    {
        return $this->qty - OrderRepository::findSumReservedCar_st($slotId,$this->id);
    }
    public function getPrices(): ActiveQuery
    {
        return $this->hasMany(Price::class, ['car_type_id' => 'id'])->orderBy('weekday,date_from');
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{car_types}}';
    }
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            FillingServiceFieldsBehavior::class,
            [
                'class' => LoggingBehavior::class,
            ],
            [
                'class' => SaveRelationsBehavior::class,
                'relations' => ['prices']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels():array
    {
        return static::getAttributeLabels();
    }
    public static function getAttributeLabels():array
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'description' => 'Описание',
            'status' => 'Статус',
            'qty' => 'Количество',
            'pwr' => 'Мощность',
            'note' => 'Примечание',
            'type' => 'Тип',
            'amocrm_field_id' => 'Соответствия поля в АмоЦРМ',

            'created_at' => 'Создано',
            'updated_at' => 'Отредактировано',
            'author_id' => 'Автор',
            'author_name' => 'Автор',
            'editor_id' => 'Редактор',
            'editor_name' => 'Редактор',
        ];
    }
    public static function getStatusList(): array
    {
        return [
            self::STATUS_ACTIVE => 'Активен',
            self::STATUS_INACTIVE => 'Не активен',
            self::STATUS_DELETED => 'Удален',
        ];
    }

    public static function statusName($status): string
    {
        return ArrayHelper::getValue(self::getStatusList(), $status);
    }

    /**
     * Находим цену по слоту
     * Сначала ищем по дню неделю с даты от
     * Если не найходим ищем по дню неделю без времени
     * Иначе ищем первую попавшую запись
     * @param Slot $slot
     * @return float
     */
    public function getPriceBySlot(Slot $slot):float
    {
        $currentPrice=null;
        foreach ($this->prices as $price) {
            if ($price->weekday) {
                if ($price->weekday == date('N',$slot->date)) {
                    if ($price->date_from) {
                        if (($price->date_from<=$slot->begin)) {
                            $currentPrice=$price->cost;
                        }
                    } else {
                        $currentPrice=$price->cost;
                    }
                }
            } else {
                if ($price->date_from) {
                    if (($price->date_from<=$slot->begin)) {
                        $currentPrice=$price->cost;
                    }
                } else {
                    $currentPrice=$price->cost;
                }
            }

        }
        return $currentPrice??Price::DEFAULT_COST;
    }


}