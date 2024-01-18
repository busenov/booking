<?php

namespace booking\helpers;

use booking\entities\Car\CarType;
use booking\entities\Schedule\Schedule;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class ScheduleHelper
{
    public static function statusLabel($status): string
    {
        switch ($status) {
            case Schedule::STATUS_INACTIVE:
                $class = 'badge bg-secondary';
                break;
            case Schedule::STATUS_ACTIVE:
                $class = 'badge bg-success';
                break;
            case Schedule::STATUS_DELETED:
                $class = 'badge bg-warning text-dark';
                break;
            default:
                $class = 'badge label-default';
        }

        return Html::tag('span', ArrayHelper::getValue(Schedule::getStatusList(), $status), [
            'class' => $class,
        ]);
    }

}