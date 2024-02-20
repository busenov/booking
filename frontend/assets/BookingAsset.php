<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class BookingAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'booking/css/main.css',
    ];
    public $js = [
        'booking/js/jquery.inputmask.min.js',
        'booking/js/main.js',
    ];
    public $depends = [
        'yii\bootstrap5\BootstrapAsset',
        'backend\assets\FontAwesomeAsset',
    ];
}
