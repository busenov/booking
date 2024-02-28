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
            case Order::STATUS_NEW:
                $class = 'badge bg-secondary';
                break;
            case Order::STATUS_RESERVATION_PROCESS:
                $class = 'badge bg-warning';
                break;
            case Order::STATUS_CHECKOUT:
                $class = 'badge bg-warning';
                break;
            case Order::STATUS_COMPLETED:
                $class = 'badge bg-success';
                break;
            case Order::STATUS_DELETED:
                $class = 'badge bg-warning text-dark';
                break;
            default:
                $class = 'badge label-default';
        }

        return Html::tag('span', ArrayHelper::getValue(Order::getStatusList(), $status), [
            'class' => $class,
        ]);
    }

}