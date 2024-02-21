<?php

namespace booking\helpers;

use booking\entities\Car\CarType;
use booking\entities\License\License;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class LicenseHelper
{
    public static function statusLabel($status): string
    {
        switch ($status) {
            case License::STATUS_INACTIVE:
                $class = 'badge bg-secondary';
                break;
            case License::STATUS_ACTIVE:
                $class = 'badge bg-success';
                break;
            case License::STATUS_DELETED:
                $class = 'badge bg-warning text-dark';
                break;
            default:
                $class = 'badge label-default';
        }

        return Html::tag('span', ArrayHelper::getValue(License::getStatusList(), $status), [
            'class' => $class,
        ]);
    }

}