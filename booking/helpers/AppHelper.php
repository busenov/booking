<?php

namespace booking\helpers;

use Yii;

class AppHelper
{
    public static function isSite():bool
    {
        return Yii::$app->id=='app-frontend';
    }
    public static function isConsole():bool
    {
        return ((Yii::$app->id=='app-console') or (Yii::$app->id=='app-common-tests2'));
    }

    public static function isDevelop():bool
    {
        return YII_ENV_DEV;
    }

    public static function datetimeFormat(int $dateTime, bool $withTime=true):string
    {
        if ($withTime) {
            return date("d.m.y H:i",$dateTime);
        } else {
            return date("d.m.y",$dateTime);
        }
    }
    public static function secondToHuman(int $second):string
    {
        $minute = 0;
        $hour = 0;
        $day = 0;
        if ($second>=60) {
            $minute = floor($second/60);
            $second = $second - ($minute*60);
        }
        if ($minute>=60) {
            $hour = floor($minute/60);
            $minute = $minute - ($hour*60);
        }
        if ($hour >=24) {
            $day =floor($hour/24);
            $hour = $hour - ($day*24);
        }

        $result='';
        if ($day) {
//            $result.='Дней: ' . $day . ' ';
            $result.=$day . 'д. ';
        }
        $result.=str_pad($hour, 2, 0, STR_PAD_LEFT). ':';

        $result.=str_pad($minute, 2, 0, STR_PAD_LEFT). ':';

        $result.= str_pad($second,2, 0, STR_PAD_LEFT);

        return $result;
    }

}