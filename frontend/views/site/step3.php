<?php


/** @var yii\web\View $this */

use frontend\assets\BookingAsset;
use yii\helpers\Url;
BookingAsset::register($this);
$this->title = Yii::$app->name;
?>

<h1>Оплата</h1>
<a href="<?= Url::to(['index','step'=>2])?>"><--Перейти к оформлению заездов</a>
<a href="<?= Url::to(['index','step'=>4])?>">Перейти к вводу доп. информации--></a>