<?php

namespace booking\entities\Slot;

use booking\entities\behaviors\FillingServiceFieldsBehavior;
use booking\entities\behaviors\LoggingBehavior;
use booking\helpers\DateHelper;
use yii\behaviors\TimestampBehavior;
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
 *
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $author_id
 * @property string|null $author_name
 * @property int|null $editor_id
 * @property string|null $editor_name
 */
class Slot extends ActiveRecord
{
    const STATUS_FREE=10;              //Свободен
    const STATUS_FROZEN=20;             //Заморожен
    const STATUS_PAID=30;               //Оплачен
    const STATUS_RESERVED=40;           //Забронирован
    const STATUS_DELETED=100;           //Удален

    public static function create(
                                int     $date,
                                int     $begin,
                                int     $end,
                                int     $qty,
                                int     $status=self::STATUS_FREE,
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
        ?string  $note=null
    ):void
    {
        $this->date=$date;
        $this->begin=$begin;
        $this->end=$end;
        $this->status=$status;
        $this->qty=$qty;
        $this->note=$note;

    }
#gets
    public static function getStatusList(): array
    {
        return [
            self::STATUS_FREE => 'Свободен',
            self::STATUS_FROZEN => 'Заморожен',
            self::STATUS_PAID => 'Оплачен',
            self::STATUS_RESERVED => 'Забронирован',
            self::STATUS_DELETED => 'Удален',
        ];
    }

    public static function getStatusName($status): string
    {
        return ArrayHelper::getValue(self::getStatusList(), $status);
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

    /**
     * Сколько свободно слотов
     * @return int
     */
    public function getFree():int
    {
        return $this->qty;
    }
#on
    public function onFree()
    {
        $this->status=self::STATUS_FREE;
    }
    public function onFrozen()
    {
        $this->status=self::STATUS_FROZEN;
    }
    public function onPaid()
    {
        $this->status=self::STATUS_PAID;
    }
    public function onReserved()
    {
        $this->status=self::STATUS_RESERVED;
    }
    public function onDeleted()
    {
        $this->status=self::STATUS_DELETED;
    }
#is
    public function isFree():bool
    {
        return $this->status===self::STATUS_FREE;
    }
    public function isFrozen():bool
    {
        return $this->status===self::STATUS_FROZEN;
    }
    public function isPaid():bool
    {
        return $this->status===self::STATUS_PAID;
    }
    public function isReserved():bool
    {
        return $this->status===self::STATUS_RESERVED;
    }
    public function isDeleted():bool
    {
        return $this->status===self::STATUS_DELETED;
    }
#hass
    public function hasReserved():bool
    {
        //TODO: когда будут заказы надо переписать метод
        return $this->qty!==$this->getFree();
    }
    /**
     * Является ли слот детским?
     * Детским слот является, если есть хотя б один слот с детской машиной
     * @return bool
     */
    public function isChild():bool
    {
        return rand(0,1);
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
            ]
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
            'end' => 'Конец',
            'status' => 'Статус',
            'qty' => 'Количество',
            'note' => 'Примечание',

            'created_at' => 'Создано',
            'updated_at' => 'Отредактировано',
            'author_id' => 'Автор',
            'author_name' => 'Автор',
            'editor_id' => 'Редактор',
            'editor_name' => 'Редактор',
        ];
    }




}