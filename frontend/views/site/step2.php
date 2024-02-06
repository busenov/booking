<?php


/** @var yii\web\View $this */

use frontend\assets\BookingAsset;
use yii\helpers\Url;
BookingAsset::register($this);
$this->title = Yii::$app->name;
?>

<h1>Оформление заезда(ов)</h1>
<a href="<?= Url::to(['index','step'=>1])?>"><--Перейти к выбору заездов</a>
<a href="<?= Url::to(['index','step'=>3])?>">Перейти к оплате--></a>