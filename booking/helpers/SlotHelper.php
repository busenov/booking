<?php

namespace booking\helpers;

use booking\entities\Car\CarType;
use booking\entities\Slot\Slot;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class SlotHelper
{
    public static function statusLabel($status): string
    {
        switch ($status) {
            case Slot::STATUS_NEW:
                $class = 'badge bg-secondary';
                break;
            case Slot::STATUS_ACTIVE:
                $class = 'badge bg-success';
                break;
            case CarType::STATUS_DELETED:
                $class = 'badge bg-warning text-dark';
                break;
            default:
                $class = 'badge label-default';
        }

        return Html::tag('span', ArrayHelper::getValue(Slot::getStatusList(), $status), [
            'class' => $class,
        ]);
    }

}