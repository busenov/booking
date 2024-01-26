<?php

namespace booking\forms\manage\Schedule;

use booking\entities\Car\CarType;
use booking\entities\Schedule\Schedule;
use booking\repositories\ScheduleRepository;
use booking\useCases\manage\ScheduleManageService;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class ScheduleForm extends Model
{
    public ?int $weekday=null;
    public ?int $begin=null;
    public ?int $end=null;
    public ?int $duration=null;
    public ?int $duration_min=null;
    public ?int $interval_min=null;
    public ?int $interval=null;
    public ?int $sort=null;
    public ?string $note=null;
    public ?int $status=null;
    public ?bool $activateSlot=false;       //активировать слот при создании. По умолчанию слот создается со статусом NEW

    public ?Schedule $_schedule;

    public function __construct(Schedule $schedule=null, $config = [])
    {
        parent::__construct($config);
        if ($schedule) {
            $this->weekday=$schedule->weekday;
            $this->begin=$schedule->begin;
            $this->end=$schedule->end;
            $this->duration=$schedule->duration;
            $this->duration_min=$schedule->duration_min;
            $this->interval=$schedule->interval;
            $this->interval_min=$schedule->interval_min;
            $this->sort=$schedule->sort;
            $this->status=$schedule->status;
            $this->note=$schedule->note;

            $this->_schedule = $schedule;
        } else {
            $this->status=Schedule::STATUS_ACTIVE;
            $this->sort = Schedule::find()->max('sort') + 1;
            $this->begin =Schedule::BEGIN_DEFAULT;
            $this->end =Schedule::END_DEFAULT;
            $this->duration_min =round(Schedule::DURATION_DEFAULT/60);
            $this->interval_min =round(Schedule::INTERVAL_DEFAULT/60);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['weekday','begin','end','duration_min','interval_min','sort',], 'integer'],
            ['begin','compare', 'compareAttribute' => 'end','operator' => '<'],
            ['end','compare', 'compareAttribute' => 'begin','operator' => '>'],
            [[ 'note'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => array_keys(Schedule::getStatusList())],
            [[ 'activateSlot'], 'boolean'],
            [['begin','end','duration_min','status','interval_min'],'required']
        ];
    }

    public function attributeLabels():array
    {
        return array_merge(Schedule::getAttributeLabels(),[
            'duration_min' => 'Продолжительность заезда(мин)',
            'interval_min' => 'Интервал между заездами(мин)',
            'activateSlot' => 'Активировать заезд после создания',


        ]);
    }

    public function getWeekdaysList():array
    {
        return Schedule::getWeekdaysList();
    }

    /**
     * @var Schedule[]|null
     */
    private ?array $_schedules=null;
    public function getSchedules():array
    {
        if (empty($this->_schedules)) {
            $this->_schedules=ScheduleRepository::findActive_st();
        }
        return $this->_schedules;
    }
    public function getScheduleList():array
    {
        return ArrayHelper::map($this->getSchedules(),'id',function (Schedule $schedule){
           return $schedule->getName();
        });
    }
}