<?php

namespace booking\helpers;

use booking\entities\Car\CarType;
use booking\entities\Order\Order;
use booking\entities\Slot\Slot;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class OrderHelper
{
    public static function statusLabel($status): string
    {
        switch ($status) {
//            case Slot::STATUS_INACTIVE:
//                $class = 'badge bg-secondary';
//                break;
//            case CarType::STATUS_ACTIVE:
//                $class = 'badge bg-success';
//                break;
//            case CarType::STATUS_DELETED:
//                $class = 'badge bg-warning text-dark';
//                break;
            default:
                $class = 'badge label-default';
        }

        return Html::tag('span', ArrayHelper::getValue(Order::getStatusList(), $status), [
            'class' => $class,
        ]);
    }

}