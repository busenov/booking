<?php

use booking\entities\Order\Order;
use booking\forms\manage\Order\OrderCreateForm;
use booking\forms\manage\Order\SlotCreateForm;
use booking\helpers\DateHelper;
use frontend\assets\BookingAsset;
use kartik\widgets\TouchSpin;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\web\YiiAsset;

/** @var yii\web\View $this */
/** @var array $calendar */
/** @var Order $order */

YiiAsset::register($this);
BookingAsset::register($this);
$this->title = Yii::$app->name;


$selected_day=array_key_exists('selected_day',$_COOKIE)?$_COOKIE['selected_day']:time();
$selected_wday=array_key_exists('selected_wday',$_COOKIE)?$_COOKIE['selected_wday']:date('N',time());
$onlyChildren= ((array_key_exists('onlyChildren',$_COOKIE))AND($_COOKIE['onlyChildren']==='true'));
$clubRaces= ((array_key_exists('clubRaces',$_COOKIE))AND($_COOKIE['clubRaces']==='true'));
$urlPre='';
$urlNxt=Url::to(['index','step'=>2]);

?>
<script type='text/javascript'>
    var $ykv_step=1;
    var $ykv_calendar=<?=json_encode($calendar)?>;
    var $ykv_order=<?=($order?json_encode($order->toJs()):'""')?>;
    var $ykv_toIssue=false;
    var $ykv_urlPre='';
    var $ykv_urlNxt='<?=$urlNxt?>';
    var $ykv_urlOrderModalAjax='<?=Url::to(['site/order-modal-ajax'])?>';
    // console.log($ykv_step);
</script>

<section class="body">
    <div class="header">
        <div class="header__wrapper">
            <div class="month" id="step1_title-date"><?=DateHelper::getMonthRu(date('n',$selected_day)-1).' '.date('Y',$selected_day)?></div>
            <a class="head-link" href="<?= Url::to(['index','step'=>2])?>">Перейти к оформлению ></a>

        </div>
    </div>
    <div class="week">
        <div class="week__days">
            <div class="week__day">ПН</div>
            <div class="week__day">ВТ</div>
            <div class="week__day">СР</div>
            <div class="week__day">ЧТ</div>
            <div class="week__day">ПТ</div>
            <div class="week__day red">СБ</div>
            <div class="week__day red">ВС</div>
        </div>
        <div id="week__dates">
            <?=$this->render('_week_dates',['calendar'=>$calendar,'week'=>time()])?>




        </div>
    </div>
    <div class="label-list">
        <div class="label-item">
            <div class="label-color green"></div>
            Доступно
        </div>
        <div class="label-item">
            <div class="label-color orange"></div>
            Осталось мало
        </div>
        <div class="label-item">
            <div class="label-color grey"></div>
            Неактивно
        </div>
        <div class="label-item">
            <div class="label-color red"></div>
            Забронировано
        </div>
    </div>
    <div class="step-title">Заезды <span id="race-day"><?=date('j',$selected_day).' '.DateHelper::getMonthRu(date('n',$selected_day)-1).' '.date('Y',$selected_day)?></span>г.</div>
    <div class="forms-block">
        <div class="for-childs">
            <img src="/booking/img/child.png" class="child-img">
            <span class="form-text">Показать только детские заезды:</span>
            <label class="switch">
                <input type="checkbox" id="only-children" <?=$onlyChildren?'checked':''?>>
                <span class="slider round"></span>
            </label>
        </div>
        <div class="clubs-enter"
             data-bs-toggle="tooltip"
             title="Для бронирование клубных заездов, необходимо иметь клубные права. Давайте проверим их:"
             data-bs-placement="top"
        >
            <img src="/booking/img/star.png" class="child-img">
            <span class="form-text">Показать клубные заезды:</span>
            <label class="switch">
                <input
                    type="checkbox"
                    id="club-races"
                    <?=$clubRaces?'checked':''?>
                >
                <span class="slider round"></span>
            </label>
        </div>
        <div class="nomer-prav <?=$clubRaces?'':'hidden'?>">
            <form class="form" action="<?=Url::to(['license/check'])?>" id="form-check_license">
                <label for="prava">Номер прав:</label>
                <input type="text" name="license" class="prava">
                <button>Проверить</button>
            </form>
        </div>
    </div>

    <div class="result-table">
    <?php
        $hour=null;
        $notSlots=true;
    ?>
    <?foreach ( $calendar[$selected_wday] as $slotId=>$item) :?>
    <?php
        if (!is_int($slotId)) continue;
        $currentHour = DateHelper::hourIntToStr($item['begin']);
        //показывать только детские?
        if ($onlyChildren AND $item['isChild']===false) {
            continue;
        }
        //показывать клубные?
        if (!$clubRaces AND $item['isClub']!==false)  {
            continue;
        }
        $notSlots=false;
        if ($currentHour!==$hour) {
            $hour=$currentHour;
            echo '<div class="result-table__time-row">' . $hour . ':00</div>';

        }
        $dateTimeStr=DateHelper::timeIntToStr($item['begin'],false).' - '. DateHelper::timeIntToStr($item['end'],false).' '. date('d.m.Y',$item['date']);

        $icon='';
        if ($item['isChild']) {
            $icon.='<img src="/booking/img/child.png" class="result-table__icon"> ';
        }
        if ($item['isClub']) {
            $icon.='<img src="/booking/img/star.png" class="result-table__icon"> ';
        }
        echo '<div class="result-table__row">';
        echo
            '   <div class="result-table__info-block">
                    <div class="result-table__time">'. DateHelper::timeIntToStr($item['begin'],false).' - '. DateHelper::timeIntToStr($item['end'],false).'</div>
                    <div class="result-table__info">'.
                        $icon .
                        $item['typeName'] .
                        ' Свободно: ' . $item['free'] . ' мест
                    </div>
                    <div class="result-table__order" id="result-table__slot_id_'.$slotId.'">'. (($order AND $order->getQtyBySlotId($slotId)>0)?$order->getQtyBySlotId($slotId):'').'</div>
                </div>
                <button 
                    class="result-table__btn btn" 
                    data-action="'.Url::to(['site/order-modal-ajax','slot_id'=>$slotId]).'"
                >Забронировать</button>
            </div>'
        ;
    ?>
    <?endforeach;?>
    <?php
        if ($notSlots) {
            echo 'Нет свободных заездов по заданным критериям';
        }
    ?>
    </div>

    <div class="timer-block">
        <div class="timer">
            <span class="timer-title">Оформить в течении</span>
            <div class="timer-time">10:00</div>
        </div>
    </div>
</section>

<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="" aria-hidden="true">
</div>
<!-- Modal -->

<pre>
<!--    --><?//dump($order);?>
<!--    --><?//dump($order->items);?>
<!--    --><?//dump($order->toJs());?>
</pre>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>