<?php

namespace booking\forms\manage\Slot;

use booking\entities\Slot\Slot;
use yii\base\Model;

class SlotForm extends Model
{
    public ?string $date=null;
    public ?string $begin=null;
    public ?string $end=null;
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
            $this->end=$slot->end;
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
            [['date','begin','end'],'integer'],
            [['note'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => array_keys(Slot::getStatusList())],
//            [['date','begin','end'], 'slotDateTimeValidator'],
//            [['date'], 'slotDateTimeValidator'],
            ['begin','compare', 'compareAttribute' => 'end','operator' => '<'],
            ['end','compare', 'compareAttribute' => 'begin','operator' => '>'],
            [['date','begin','end','qty','status'],'required']
        ];
    }

    public function attributeLabels():array
    {
        return Slot::getAttributeLabels();
    }
//    public function slotDateTimeValidator($attribute,$params)
//    {
//        dump('tut');
//        dump($attribute);
//        $this->addError($attribute, 'your password is not strong enough!');
//        return "
////if(".$condition.") {
//    messages.push(your password is too weak, you fool!');
////}
//";
////        exit;
//    }
}