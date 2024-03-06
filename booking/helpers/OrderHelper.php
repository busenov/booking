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
                $class = 'badge bg-info';
                break;
            case Order::STATUS_SENT_AMOCRM:
                $class = 'badge bg-info';
                break;
            case Order::STATUS_SAVED_ADDITION_INFO:
                $class = 'badge bg-dark';
                break;
            case Order::STATUS_COMPLETED:
                $class = 'badge bg-success';
                break;
            case Order::STATUS_DELETED:
                $class = 'badge bg-danger';
                break;
            default:
                $class = 'badge bg-light';
        }

        return Html::tag('span', ArrayHelper::getValue(Order::getStatusList(), $status), [
            'class' => $class,
        ]);
    }

}