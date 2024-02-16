<?php

namespace booking\forms\manage\License;

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
    public ?License $_license;
    public function __construct(License $license=null, $config = [])
    {
        parent::__construct($config);
        if ($license) {
            $this->number=$license->number;
            $this->note=$license->note;
            $this->status=$license->status;
            $this->date=$license->date;

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
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['status'], 'in', 'range' => array_keys(License::getStatusList())],
            [['number','user_id','status'],'required']
        ];
    }

    public function attributeLabels():array
    {
        return License::getAttributeLabels();
    }

}