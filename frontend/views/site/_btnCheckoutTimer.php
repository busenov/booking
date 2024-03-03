<?php

/** @var yii\web\View $this */
/** @var ?Order $order      текущий заказ*/

use booking\entities\Order\Order;
use booking\helpers\DateHelper;
use yii\helpers\Url;

$url= Url::to(['index','step'=>2]);
?>

<div class="timer-block <?=(($order && $order->isReservationProcess())?'':'hidden') ?>" id="btn_timer" data-action="<?=$url?>">
    <div class="timer">
        <span class="timer-title">Оформить в течении</span>
<!--        <div-->
<!--                class="timer-time"-->
<!--                id="timer-time"-->
<!--                data-time="--><?//=$order?$order->getLeftTimeReserve():''?><!--"-->
<!--        >--><?//= $order?DateHelper::minuteIntToStr($order->getLeftTimeReserve()):''?><!--</div>-->
    </div>
</div>

<?php
$js = <<<JS
    let 
        timerTimeEl=document.getElementById('timer-time'),
        time
        ;
    if (timerTimeEl) {
        time=timerTimeEl.dataset.time;
        setInterval(function () {
            if (time<0) {
                 clearInterval(this);
            }
            timerTimeEl.innerHTML = time--;
        }, 1000);
    }
JS;
//$this->registerJs($js);
?>