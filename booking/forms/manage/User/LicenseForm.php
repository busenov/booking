<?php

namespace booking\forms\manage\User;

use booking\entities\License\License;
use booking\entities\User\User;
use yii\base\Model;

class LicenseForm extends Model
{
    public ?int $number=null;
    public ?string $note=null;
    public ?int $status=null;
    public ?int $user_id=null;
    public ?int $date=null;
    public ?string $surname=null;
    public ?string $name=null;
    public ?string $telephone=null;
    public ?License $_license;
    public function __construct(License $license=null, $config = [])
    {
        parent::__construct($config);
        if ($license) {
            $this->number=$license->number;
            $this->note=$license->note;
            $this->status=$license->status;
            $this->date=$license->date;
            $this->surname=$license->user->surname;
            $this->name=$license->user->name;
            $this->telephone=$license->user->telephone;

            $this->_license = $license;
        } else {
            $this->status=License::STATUS_ACTIVE;
        }

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number',], 'integer', 'min'=>1],
            [['date'], 'integer'],
            ['number', 'unique', 'targetClass' => '\booking\entities\License\License', 'filter' => function ($query) {
                $query->andWhere(['not', ['number'=>$this->number]]);
            },
                'message' => 'Такой номер уже есть в системе'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['status'], 'in', 'range' => array_keys(License::getStatusList())],
            [['surname','name','note'],'string','max'=>255],
            [['number','telephone','status','surname'],'required']
        ];
    }

    public function attributeLabels():array
    {
        return array_merge(License::getAttributeLabels(),[
            'telephone' => 'Телефон',
            'surname' => 'Фамилия',
            'name' => 'Имя',
        ]);
    }

}