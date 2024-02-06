<?php


/** @var yii\web\View $this */

use frontend\assets\BookingAsset;
use yii\helpers\Url;
BookingAsset::register($this);
$this->title = Yii::$app->name;
?>

<h1>Ввод дополнительной информации</h1>
<a href="<?= Url::to(['index','step'=>3])?>"><--Перейти к оплате</a>
