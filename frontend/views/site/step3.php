<?php


/** @var yii\web\View $this */

use frontend\assets\BookingAsset;
use yii\helpers\Url;
use yii\web\YiiAsset;

YiiAsset::register($this);
BookingAsset::register($this);
$this->title = Yii::$app->name;

$urlPre=Url::to(['index','step'=>2]);
$urlNxt=Url::to(['index','step'=>4]);
?>

<div class="body">
    <div class="header next-step">
        <div class="header__wrapper">
            <a class="head-link prev" href="<?= $urlPre?>">< Вернуться к оформление</a>
            <div class="month noIcon">Оплата</div>
            <a class="head-link" href="<?= $urlNxt?>">Перейти к вводу дополнительной информации ></a>
        </div>
    </div>
</div>
