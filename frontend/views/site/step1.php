<?php


/** @var yii\web\View $this */

use frontend\assets\BookingAsset;
use yii\helpers\Url;
BookingAsset::register($this);
$this->title = Yii::$app->name;
?>

<h1>Выбор заезда</h1>
<a href="<?= Url::to(['index','step'=>2])?>">Перейти к оформлению--></a>