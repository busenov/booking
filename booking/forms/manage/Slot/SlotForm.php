<?php

namespace booking\forms\manage\Slot;

use booking\entities\Slot\Slot;
use yii\base\Model;

class SlotForm extends Model
{
    public ?int $date=null;
    public ?int $begin=null;
    public ?int $end=null;
    public ?int $status=null;
    public ?int $qty=null;
    public ?string $note=null;
    public ?Slot $_slot;
    public function __construct(Slot $slot=null, $config = [])
    {
        parent::__construct($config);
        if ($slot) {
            $this->date=$slot->date;
            $this->begin=$slot->begin;
            $this->end=$slot->note;
            $this->note=$slot->note;
            $this->status=$slot->status;
            $this->qty=$slot->qty;

            $this->_slot = $slot;
        } else {
            $this->status=Slot::STATUS_FREE;
        }

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qty',], 'integer', 'min'=>1],
            [['date'],'integer'],
            [['begin','end'],'integer', 'min'=>1],
            [['note'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => array_keys(Slot::getStatusList())],
            [['date','qty','begin','end','status'],'required']
        ];
    }

    public function attributeLabels():array
    {
        return Slot::getAttributeLabels();
    }

}