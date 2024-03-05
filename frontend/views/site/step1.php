<?php

use booking\entities\Order\Order;
use booking\forms\manage\Order\LicenseForm;
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
/** @var LicenseForm $licenseForm */

YiiAsset::register($this);
BookingAsset::register($this);
$this->title = Yii::$app->name;


$selected_day=array_key_exists('selected_day',$_COOKIE)?$_COOKIE['selected_day']:time();
$selected_wday=array_key_exists('selected_wday',$_COOKIE)?$_COOKIE['selected_wday']:date('N',time());
$onlyChildren= ((array_key_exists('onlyChildren',$_COOKIE))AND($_COOKIE['onlyChildren']==='true'));
$clubRaces= ((array_key_exists('clubRaces',$_COOKIE))AND($_COOKIE['clubRaces']==='true'));
$urlPre='';
$urlNxt=Url::to(['index','step'=>2]);
$hasItems= $order && !empty($order->items);
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
            <div class="week__day blue">СБ</div>
            <div class="week__day blue">ВС</div>
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
    <div class="head-timer <?=($hasItems?'':'hidden') ?>" id="timer-time-block">
        Время действия брони
        <div
                class="timer-time"
                id="timer-time"
                data-time="<?=$order?$order->getLeftTimeReserve():''?>"
        ><?= $order?DateHelper::minuteIntToStr($order->getLeftTimeReserve()):''?></div>
    </div>
    <div class="order-block">
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
                <?php $form = ActiveForm::begin([
                    'class'=>"form",
                    'method'=>"post",
                    'id'=> 'form-check_license',
                    'action'=>Url::to(['check-license'])
                ]); ?>
                    <label for="prava">Номер прав:</label>
                <?= $form->field($licenseForm, 'number',)->input([
                    'class'=>'prava',
                    'template'=>'{input}',
                    'id'=>'license_number222'
                ])->label(false) ?>
                    <button>Проверить</button>
                <?php ActiveForm::end(); ?>
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

            $icon='
                    <div class="result-table__info-icon open">
                        <img src="/booking/img/grownup-icon.png">
                        <span>Взрослый<br> заезд</span>
                    </div>
            ';
            if ($item['isChild']) {
                $icon='
                        <div class="result-table__info-icon open " >
                            <img src="/booking/img/child-big.png">
                            <span>Детский<br> заезд</span>
                        </div>
            ';
            }
            if ($item['isClub']) {
                $icon='
                        <div class="result-table__info-icon open">
                            <img src="/booking/img/star.png">
                            <span>Клубный<br> заезд</span>
                        </div>
            ';
            }
            echo '<div class="result-table__row">';
            echo
                '   <div class="result-table__info-block">
                        <div class="result-table__time">'. DateHelper::timeIntToStr($item['begin'],false).' - '. DateHelper::timeIntToStr($item['end'],false).'</div>
                        <div class="result-table__info">



                        '.
                            $icon .
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

    </div>

    <?=$this->render('_btnCheckoutTimer',[
        'order'=>$order])
    ?>


</section>

<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="" aria-hidden="true">
</div>
<!-- Modal -->

<pre>
    <?dump($order);?>
<!--    --><?//if ($order) :?>
<!--    --><?//dump($order->statusName($order->status));?>
<!--    --><?//dump($order->customer);?>
<!--    --><?//dump($order->date_begin_reserve?date('d/m/y H:i',$order->date_begin_reserve):'');?>
<!--    --><?//dump($calendar);?>
<!--    --><?//dump($order->getQtyBySlotId(7634));?>
<!--    --><?//endif;?>
</pre>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>