<?php

namespace booking\forms\manage\Slot;

use booking\entities\Slot\Slot;
use booking\repositories\CarTypeRepository;
use yii\base\Model;

class SlotForm extends Model
{
    public ?string $date=null;
    public ?string $begin=null;
    public ?string $end=null;
    public ?int $status=null;
    public ?int $qty=null;
    public ?string $note=null;
    public ?bool $is_child=null;
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
            $this->is_child=$slot->is_child;

            $this->_slot = $slot;
        } else {
            $this->status=Slot::STATUS_NEW;
        }

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $maxQty=Slot::getMaxQty();
        return [
            [['qty',], 'integer', 'min'=>1,'max'=>$maxQty],
            [['date','begin','end'],'integer'],
            [['note'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => array_keys(Slot::getStatusList())],
//            [['date','begin','end'], 'slotDateTimeValidator'],
//            [['date'], 'slotDateTimeValidator'],
            ['begin','compare', 'compareAttribute' => 'end','operator' => '<'],
            ['end','compare', 'compareAttribute' => 'begin','operator' => '>'],
            ['is_child','boolean'],
            [['date','begin','end','qty','status'],'required']
        ];
    }

    public function attributeLabels():array
    {
        return Slot::getAttributeLabels();
    }
    /**
     * Иногда нам надо отдать значение не как в базе идентификатор, а человечески понятное значение.
     * Например не client_id, а имя клиента, не type, а название типа
     * @param $attributeName
     */
    public function getValue($attributeName)
    {
        switch ($attributeName) {
            case 'is_child':
                return Slot::getIsChildLabel($this->is_child);
//            case 'priority':
//                return Slot::getPriorityLabel($this->priority);
//            case 'client_id':
//                return $this->getClientName($this->client_id);
//            case 'responsible_id':
//                return $this->getResponsibleName($this->responsible_id);
//            case 'status':
//                return $this->getStatusName($this->status);
            default :
                return $this->$attributeName;
        }
    }
}