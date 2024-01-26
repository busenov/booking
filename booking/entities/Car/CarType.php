<?php

namespace booking\entities\Car;

use booking\entities\behaviors\FillingServiceFieldsBehavior;
use booking\entities\behaviors\LoggingBehavior;
use booking\entities\Order\Order;
use booking\entities\Slot\Slot;
use booking\repositories\OrderRepository;
use booking\repositories\SlotRepository;
use yii\behaviors\TimestampBehavior;
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
 *
 *
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $author_id
 * @property string|null $author_name
 * @property int|null $editor_id
 * @property string|null $editor_name
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
                                ?string $note=null
                            ):self
    {
        return new self([
            'name'=>$name,
            'description'=>$description,
            'status'=>$status,
            'qty'=>$qty,
            'pwr'=>$pwr,
            'note'=>$note
        ]);
    }
    public function edit(
        string  $name,
        string  $description,
        int     $status=self::STATUS_ACTIVE,
        int     $qty = 1,
        ?float  $pwr=null,
        ?string $note=null
    ):void
    {
        $this->name=$name;
        $this->description=$description;
        $this->status=$status;
        $this->qty=$qty;
        $this->pwr=$pwr;
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
    public function getFreeBySlot(int $slotId=null)
    {
        return $this->qty - OrderRepository::findSumReservedCar_st($slotId,$this->id);
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
            'name' => 'Название',
            'description' => 'Описание',
            'status' => 'Статус',
            'qty' => 'Количество',
            'pwr' => 'Мощность',
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


}