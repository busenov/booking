<?php

namespace booking\forms\manage\Order;

use booking\entities\Car\CarType;
use booking\entities\License\License;
use booking\entities\User\User;
use yii\base\Model;

class LicenseForm extends Model
{
    public ?string $number=null;
    public ?License $_license;
    public function __construct(License $license=null, $config = [])
    {
        parent::__construct($config);
        if ($license) {
            $this->number=$license->number;
            $this->_license = $license;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number'], 'integer'],
            [['number'], 'exist', 'skipOnError' => true, 'targetClass' => License::class, 'targetAttribute' => ['number' => 'number']],
        ];
    }

    public function attributeLabels():array
    {
        return License::getAttributeLabels();
    }

}