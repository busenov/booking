<?php

namespace booking\helpers;

use booking\entities\User\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class UserHelper
{
    public static function statusLabel($status): string
    {
        switch ($status) {
            case User::STATUS_WAIT:
                $class = 'badge bg-secondary';
                break;
            case User::STATUS_ACTIVE:
                $class = 'badge bg-success';
                break;
            case User::STATUS_DELETED:
                $class = 'badge bg-warning text-dark';
                break;
            default:
                $class = 'label label-default';
        }

        return Html::tag('span', ArrayHelper::getValue(User::getStatusList(), $status), [
            'class' => $class,
        ]);
    }
}