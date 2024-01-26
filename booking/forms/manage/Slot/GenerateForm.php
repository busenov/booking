<?php

namespace booking\forms\manage\Slot;

use booking\entities\Car\CarType;
use booking\entities\Schedule\Schedule;
use booking\forms\manage\Schedule\ScheduleForm;
use booking\useCases\manage\ScheduleManageService;
use yii\base\Model;

class GenerateForm extends ScheduleForm
{
    public ?int $scheduleId=null;

    public function __construct(Schedule $schedule=null, $config = [])
    {
        parent::__construct($schedule,$config);
        if ($schedule) {
            $this->scheduleId=$schedule->id;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [[ 'scheduleId'], 'integer'],
                [['scheduleId'], 'exist', 'skipOnError' => true, 'targetClass' => Schedule::class, 'targetAttribute' => ['scheduleId' => 'id']],
            ]
        );
    }

    public function attributeLabels():array
    {
        return array_merge(parent::attributeLabels(),[
            'scheduleId' => 'Расписание',
        ]);
    }

    public function load($data, $formName = null)
    {
        $result=parent::load($data, $formName);
        $this->duration=$this->duration_min?$this->duration_min*60:null;
        $this->interval=$this->interval_min?$this->interval_min*60:null;
        return $result;
    }

}