<?php

namespace frontend\bootstrap;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\web\Application;
use yii\web\Controller;

class SetUp implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $container = \Yii::$container;
    }
}