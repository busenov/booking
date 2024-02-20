<?php

namespace booking\entities\License;

use booking\entities\behaviors\FillingServiceFieldsBehavior;
use booking\entities\behaviors\LoggingBehavior;
use booking\entities\Order\Order;
use booking\entities\Slot\Slot;
use booking\entities\User\User;
use booking\repositories\OrderRepository;
use booking\repositories\SlotRepository;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "licenses".
 *
 * @property int $id
 * @property int|null $number
 * @property int|null $user_id
 * @property int|null $status
 * @property int|null $date
 * @property string|null $note
 *
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $author_id
 * @property string|null $author_name
 * @property int|null $editor_id
 * @property string|null $editor_name
 *
 * @property User $user
 */
class License extends ActiveRecord
{
    const STATUS_ACTIVE=10;              //Активный
    const STATUS_INACTIVE=5;             //Не активный
    const STATUS_DELETED=100;            //Удален

    public static function create(
                                int  $number,
                                int  $user_id,
                                int     $status=self::STATUS_ACTIVE,
                                ?int $date=null,
                                ?string $note=null
                            ):self
    {
        return new self([
            'number'=>$number,
            'user_id'=>$user_id,
            'status'=>$status,
            'date'=>$date??time(),
            'note'=>$note
        ]);
    }
    public function edit(
        int  $number,
        int  $user_id,
        int     $status=self::STATUS_ACTIVE,
        ?int $date=null,
        ?string $note=null
    ):void
    {
        $this->number=$number;
        $this->user_id=$user_id;
        $this->status=$status;
        $this->date=$date;
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
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'customer_id']);
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{licenses}}';
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
            'number' => 'Номер',
            'status' => 'Статус',
            'date' => 'Дата выдачи',
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