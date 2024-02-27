<?php

namespace booking\entities\Car;

use booking\entities\behaviors\FillingServiceFieldsBehavior;
use booking\entities\behaviors\LoggingBehavior;
use booking\entities\Order\Order;
use booking\entities\Slot\Slot;
use booking\repositories\OrderRepository;
use booking\repositories\SlotRepository;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "car_prices".
 *
 * @property int $id
 * @property int $car_type_id
 * @property int $weekday
 * @property int|null $date_from
 * @property int|null $status
 * @property double $cost
 * @property string $note
 *
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $author_id
 * @property string|null $author_name
 * @property int|null $editor_id
 * @property string|null $editor_name
 *
 * @property CarType $carType
 */
class Price extends ActiveRecord
{
    const STATUS_ACTIVE=10;              //Активный
    const STATUS_INACTIVE=5;             //Не активный
    const STATUS_DELETED=100;            //Удален
    const DEFAULT_COST = 1000;

    public static function create(
                                float   $cost,
                                ?int  $weekday=null,
                                ?int  $date_from=null,
                                int     $status=self::STATUS_ACTIVE,
                                ?string $note=null
                            ):self
    {
        return new self([
            'cost'=>$cost,
            'weekday'=>$weekday,
            'date_from'=>$date_from,
            'status'=>$status,
            'note'=>$note,
        ]);
    }
    public function edit(
        float   $cost,
        string  $weekday,
        string  $date_from,
        int     $status=self::STATUS_ACTIVE,
        ?string $note=null
    ):void
    {
        $this->cost=$cost;
        $this->weekday=$weekday;
        $this->date_from=$date_from;
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
    public function isIdEqualTo($id):bool
    {
        return $this->id == $id;
    }
#gets
    public function getCarType(): ActiveQuery
    {
        return $this->hasOne(CarType::class, ['id' => 'car_type_id']);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{car_prices}}';
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
            'car_type_id' => 'Машина(карт)',
            'weekday' => 'День недели',
            'date_from' => 'С какого времени',
            'status' => 'Статус',
            'cost' => 'Цена',
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
    public static function getWeekdayList(): array
    {
        return [
            1 => 'Понедельник',
            2 => 'Вторник',
            3 => 'Среда',
            4 => 'Четверг',
            5 => 'Пятница',
            6 => 'Суббота',
            7 => 'Воскресенье',
        ];
    }
    public static function weekdayName($weekday): string
    {
        return ArrayHelper::getValue(self::getWeekdayList(), $weekday);
    }

}