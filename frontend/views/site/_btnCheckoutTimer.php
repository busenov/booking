<?php

/** @var yii\web\View $this */
/** @var int $timer         кол-во секунд */
/** @var int $time          текущее время*/

use booking\helpers\DateHelper;
use yii\helpers\Url;

$url= Url::to(['index','step'=>2]);
?>

<div class="timer-block" id="btn_timer" data-action="<?=$url?>" data-timer="<?=$timer?>" data-time="<?=$time?>">
    <div class="timer">
        <span class="timer-title">Оформить в течении</span>
        <div class="timer-time"><?= $time?DateHelper::minuteIntToStr($time):''?></div>
    </div>
</div>
