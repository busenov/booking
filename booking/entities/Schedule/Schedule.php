<?php

namespace booking\entities\Schedule;

use booking\entities\behaviors\FillingServiceFieldsBehavior;
use booking\entities\behaviors\LoggingBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "schedule".
 *
 * @property int $id
 * @property int $weekday               //день недели
 * @property int $begin                 //начало дня(сек)
 * @property int $end                   //конец дня(сек)
 * @property int $duration              //длительность заезда(сек)
 * @property int $interval              //интервал между слотами(сек)
 * @property int $sort                  //сортировка
 *
 * @property string|null $note
 * @property int|null $status
 *
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $author_id
 * @property string|null $author_name
 * @property int|null $editor_id
 * @property string|null $editor_name
 */
class Schedule extends ActiveRecord
{
    const STATUS_ACTIVE=10;              //Активный
    const STATUS_INACTIVE=5;             //Не активный
    const STATUS_DELETED=100;            //Удален

    const DURATION_DEFAULT=600;          //Время заезда по умолчанию 10 минут
    const INTERVAL_DEFAULT=900;          //Интервалы между заездами по умолчанию 15 минут
    const BEGIN_DEFAULT=28800;
    const END_DEFAULT=82800;


    const WEEKDAY_WORK=-1;
    const WEEKDAY_WEEKEND=-2;

    public static function create(
                                int  $begin,
                                int  $end,
                                int  $duration=self::DURATION_DEFAULT,
                                int  $interval=self::INTERVAL_DEFAULT,
                                ?int $sort=null,
                                ?int  $weekday=null,
                                int     $status=self::STATUS_ACTIVE,
                                string  $note=null
                            ):self
    {
        return new self([
            'begin'=>$begin,
            'end'=>$end,
            'duration'=>$duration,
            'interval'=>$interval,
            'sort'=>$sort,
            'weekday'=>$weekday,
            'status'=>$status,
            'note'=>$note
        ]);
    }
    public function edit(
        int  $begin,
        int  $end,
        int  $duration=self::DURATION_DEFAULT,
        int  $interval=self::INTERVAL_DEFAULT,
        ?int $sort=null,
        ?int  $weekday=null,
        int     $status=self::STATUS_ACTIVE,
        string  $note=null
    ):void
    {
        $this->begin=$begin;
        $this->end=$end;
        $this->duration=$duration;
        $this->interval=$interval;
        $this->sort=$sort;
        $this->weekday=$weekday;
        $this->status=$status;
        $this->note=$note;
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
#gets
    public static function getAttributeLabels():array
    {
        return [
            'id' => 'ID',
            'weekday' => 'День недели',
            'begin' => 'Начало',
            'end' => 'Конец',
            'duration' => 'Продолжительность заезда',
            'interval' => 'Интервал между заездами',
            'sort' => 'сортировка',
            'status' => 'Статус',
            'note' => 'Примечание',

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
    public static function getWeekdaysList():array
    {
        return [
            self::WEEKDAY_WORK      => 'БУДНИ',
            self::WEEKDAY_WEEKEND   => 'ВЫХОДНЫЕ',
            1                       => 'Понедельник',
            2                       => 'Вторник',
            3                       => 'Среда',
            4                       => 'Четверг',
            5                       => 'Пятница',
            6                       => 'Суббота',
            7                       => 'Воскресенье',
        ];
    }
    public static function weekdayName($weekday): string
    {
        return ArrayHelper::getValue(self::getWeekdaysList(), $weekday);
    }

    public static function getWeekdaysWeekend():array
    {
        return [6,7];
    }
    public static function getWeekdaysWork():array
    {
        return [1,2,3,4,5];
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{schedules}}';
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

}