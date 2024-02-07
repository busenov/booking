<?php

namespace booking\entities\Slot;

use booking\entities\behaviors\FillingServiceFieldsBehavior;
use booking\entities\behaviors\LoggingBehavior;
use booking\entities\Order\Order;
use booking\helpers\DateHelper;
use booking\repositories\CarTypeRepository;
use booking\repositories\OrderRepository;
use Codeception\Constraint\Page;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "slots".
 *
 * @property int $id
 * @property int $date                  //секунды на начало дня
 * @property int $begin                 //время начала в секундах. Например время: 13:15 = 13*60*60 + 15*60
 * @property int $end                   //время окончания в секундах
 * @property int $status                //статус
 * @property int $qty                   //возможное кол-во людей
 *
 * @property string|null $note          //примечание
 * @property boolean $is_child          //Детский заезд?
 * @property int $type                  //Тип заезда(взрослый, детский, клубный)
 *
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $author_id
 * @property string|null $author_name
 * @property int|null $editor_id
 * @property string|null $editor_name
 *
 * @property Order[] $orders
 */
class Slot extends ActiveRecord
{
    const STATUS_NEW=10;                //Новый
    const STATUS_ACTIVE=20;             //Активный
    const STATUS_DELETED=100;           //Удален
    const TYPE_ADULT=10;                //Взрослый(по умолчанию)
    const TYPE_CHILD=20;                //Детский
    const TYPE_CLUB=30;                 //Клубный

    public static function create(
                                int     $date,
                                int     $begin,
                                int     $end,
                                int     $qty,
                                int     $status=self::STATUS_NEW,
                                ?string  $note=null
                            ):self
    {
        return new self([
            'date'=>$date,
            'begin'=>$begin,
            'end'=>$end,
            'qty'=>$qty,
            'status'=>$status,
            'note'=>$note
        ]);
    }




    public function edit(
        int     $date,
        int     $begin,
        int     $end,
        int     $qty,
        int     $status,
        bool     $isChild,
        ?string  $note=null
    ):void
    {
        $this->date=$date;
        $this->begin=$begin;
        $this->end=$end;
        $this->status=$status;
        $this->qty=$qty;
        $this->is_child=$isChild;
        $this->note=$note;

    }
#gets
    public static function getStatusList(): array
    {
        return [
            self::STATUS_NEW => 'Новый',
            self::STATUS_ACTIVE => 'Активный',
            self::STATUS_DELETED => 'Удален',
        ];
    }

    public static function getStatusName($status): string
    {
        return ArrayHelper::getValue(self::getStatusList(), $status);
    }
    public static function getTypeList(): array
    {
        return [
            self::TYPE_ADULT => 'Взрослый',
            self::TYPE_CHILD => 'Детский',
            self::TYPE_CLUB => 'Клубный',
        ];
    }

    public static function getTypeName($type): string
    {
        return ArrayHelper::getValue(self::getTypeList(), $type);
    }

    public function getName(): string
    {
        return date('d.m.y',$this->date) . ' ' . DateHelper::timeIntToStr($this->begin,false) .' - ' . DateHelper::timeIntToStr($this->end,false);
    }

    /**
     * Возвращаем время начало слота в формате Unixtime
     * @return int
     */
    public function getBeginUT():int
    {
        return ($this->date + $this->begin);
    }

    /**
     * Возвращаем время окончания слота в формате Unixtime
     * @return int
     */
    public function getEndUT():int
    {
        return ($this->date + $this->end);
    }
    public function getOrders(): ActiveQuery
    {
        return $this->hasMany(Order::class, ['slot_id' => 'id']);
    }

    /**
     * Сколько свободно слотов. Если передали $carTypeId, тогда смотрим сколько можем заказать именно этой машины
     * Например.
     * По слоту у нас возможно участвовать 10 людей. Машин с типом 2 у нас всего 5, причем 1 уже заказали. Поэтому
     * в рамках этого слота машину с типом 2 у нас можно заказать только 4, но всевго машин можно заказать 9
     * @param int|null $carTypeId
     * @return int
     */
    public function getFree(int $carTypeId=null):int
    {
        $carType=CarTypeRepository::find_st($carTypeId);
        if ($reserved=OrderRepository::findSumReservedCar_st($this->id) and $reserved[$this->id]['qty'] ) {
            $allReservedCnt=$reserved[$this->id]['qty'];
            $allFree=$this->qty-$allReservedCnt;

            if ($carTypeId) {
                $reservedCar=$reserved[$this->id][$carTypeId];
                $freeCarByType=$carType->qty - $reservedCar;
                if ($freeCarByType<$allFree) {
                    return $freeCarByType;
                }
            }
            return $allFree;

        } else {
            return $this->qty;
        }
    }
    public static function getIsChildLabels():array
    {
        return [
            true => 'детский',
            false => 'взрослый'
        ];
    }
    public static function getIsChildLabel($attribute)
    {
        $result=ArrayHelper::getValue(self::getIsChildLabels(), $attribute);
        return $result??$attribute;
    }
    public static function getMaxQty():int
    {
        return \Yii::$app->params['slot.maxQty'];
    }
#on
    public function onNew()
    {
        $this->status=self::STATUS_NEW;
    }
    public function onActive()
    {
        $this->status=self::STATUS_ACTIVE;
    }
    public function onDeleted()
    {
        $this->status=self::STATUS_DELETED;
    }
#is
    public function isNew():bool
    {
        return $this->status===self::STATUS_NEW;
    }
    public function isActive():bool
    {
        return $this->status===self::STATUS_ACTIVE;
    }
    public function isDeleted():bool
    {
        return $this->status===self::STATUS_DELETED;
    }
    /**
     * Является ли слот детским?
     * @return bool
     */
    public function isChild():bool
    {
        return $this->type===self::TYPE_CHILD;
    }
    public function isClub():bool
    {
        return $this->type===self::TYPE_CLUB;
    }
    public function isAdult():bool
    {
        return $this->type===self::TYPE_ADULT;
    }
#hass
    public function hasReserved():bool
    {
        //TODO: когда будут заказы надо переписать метод
        return $this->qty!==$this->getFree();
    }

    public function hasOrders():bool
    {
        return count($this->orders)>0;
    }
    /**
     * Разрешено ли редактировать при след. условиях:
     * - если нет заказов на этот сло
     *
     * @return bool
     */
    public function readOnly():bool
    {
        if ($this->hasOrders()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{slots}}';
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
                'relations' => ['orders'],
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
            'date' => 'Дата',
            'begin' => 'Начало',
            'end' => 'Окончание',
            'status' => 'Статус',
            'qty' => 'Количество',
            'note' => 'Примечание',
            'is_child' => 'Детский',
            'countOrders' => 'Броней',

            'created_at' => 'Создано',
            'updated_at' => 'Отредактировано',
            'author_id' => 'Автор',
            'author_name' => 'Автор',
            'editor_id' => 'Редактор',
            'editor_name' => 'Редактор',
        ];
    }




}