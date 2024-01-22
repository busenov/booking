<?php

namespace backend\assets;

use yii\web\AssetBundle;

class CalendarAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/calendar/style.css',
    ];
    public $js = [
        'js/calendar/main.js',
        'js/calendar/popper.js',
    ];
    public $depends = [
        'yii\bootstrap5\BootstrapAsset',
        'backend\assets\FontAwesomeAsset',
    ];
}
