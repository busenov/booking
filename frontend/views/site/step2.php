<?php

use booking\entities\Order\Order;
use booking\entities\Slot\Slot;
use booking\forms\manage\Order\CustomerForm;
use booking\helpers\DateHelper;
use frontend\assets\BookingAsset;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\web\YiiAsset;

/** @var yii\web\View $this */
/** @var array $calendar */
/** @var Order $order */
/** @var CustomerForm $customerOrder */

YiiAsset::register($this);
BookingAsset::register($this);
$this->title = Yii::$app->name;

$selected_day=array_key_exists('selected_day',$_COOKIE)?$_COOKIE['selected_day']:time();
$selected_wday=array_key_exists('selected_wday',$_COOKIE)?$_COOKIE['selected_wday']:date('N',time());
$onlyChildren= ((array_key_exists('onlyChildren',$_COOKIE))AND($_COOKIE['onlyChildren']==='true'));
$clubRaces= ((array_key_exists('clubRaces',$_COOKIE))AND($_COOKIE['clubRaces']==='true'));
$urlPre=Url::to(['index','step'=>1]);
$urlNxt=Url::to(['index','step'=>3]);
?>
<script type='text/javascript'>
    var $ykv_step=2;
</script>

<div class="body">
    <div class="header next-step">
        <div class="header__wrapper">
            <a class="head-link prev" href="<?= $urlPre?>">< Вернуться к выбору даты и времени</a>
            <div class="month noIcon">Оформление заказа</div>
            <a class="head-link" href="<?= $urlNxt?>">Перейти к оплате ></a>
        </div>
    </div>
    <? if ($order->items):?>
    <?php
        $currentSlotId=null;
    ?>
    <? foreach ($order->items as $item) :?>
    <? if ($currentSlotId!==$item->slot_id):?>
        <?if (!empty($currentSlotId)) :?>
            </div>
        <? endif;?>
        <?php $currentSlotId=$item->slot_id?>
    <div class="order-table">
        <div class="order-table__head">
            <div class="order-table__date"><?=$item->slot->getDateStr()?></div>
            <div class="order-table__time"><?=$item->slot->getTimeStr()?></div>
            <div class="order-table__type"><?=$item->slot->getTypeNameStr()?></div>
            <div class="order-table__name"></div>
            <div class="order-table__col"></div>
            <div class="order-table__price">Цена</div>
            <div class="order-table__summa">Сумма</div>
            <div class="order-table__close"></div>
            <a href="<?=Url::to(['delete-slot','slot'=>$item->slot_id])?>" data-confirm="Вы действительно хотите удалить заезд из заказа?">
                <div class="order-table__close"></div>
            </a>
        </div>
    <? endif;?>
        <div class="order-table__row">
            <div class="order-table__date"></div>
            <div class="order-table__time"></div>
            <div class="order-table__type"></div>
            <div class="order-table__name"><?=$item->carType->name?></div>
            <div class="order-table__col">
                <div class="number">
                    <span class="minus btn-inc" data-inc="-" >-</span>
                    <input type="text" value="<?=$item->qty?>" data-action="<?=Url::to(['change-qty-to-order-ajax','item'=>$item->id])?>"/>
                    <span class="plus btn-inc" data-inc="+" >+</span>
                </div>
            </div>
            <div class="order-table__price" id="price_slot_id_<?=$item->slot_id?>_cartype_id_<?=$item->carType_id?>" ><?=number_format($item->price,0,'',' ')?></div>
            <div class="order-table__summa">
                <span id="total_slot_id_<?=$item->slot_id?>_cartype_id_<?=$item->carType_id?>"><?=number_format($item->total,0,'',' ')?></span>
                <a href="<?=Url::to(['delete-item','item'=>$item->id])?>" data-confirm="Вы действительно хотите удалить позицию в заезде?">
                    <div class="order-table__delete"></div>
                </a>
            </div>
        </div>
    <?endforeach;?>
    </div>

    <div class="summ-block">
        ИТОГО: <div class="summ-block__result" id="order-total"><?=number_format($order->total,0,'',' ')?></div> руб.
    </div>
    <div class="order-form__block">
        <div class="order-form__title">Данные для заказа</div>
        <?php $form = ActiveForm::begin([
            'options'=>[
                'class'=>"order-form",
            ],
            'method'=>"post",
            'action'=>Url::to(['index','step'=>2])
        ]); ?>
        <div class="order-form__item">
            <?= $form->field($customerOrder, 'name')->textInput(['maxlength' => true,]) ?>
        </div>
        <div class="order-form__item">
            <?= $form->field($customerOrder, 'surname')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="order-form__item">
            <?= $form->field($customerOrder, 'telephone')->textInput(['maxlength' => true,'placeholder'=>"+7(___) ___ -___-___*"]) ?>
        </div>
        <div class="order-form__item">
            <?= $form->field($customerOrder, 'email')->textInput(['maxlength' => true]) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <?=$this->render('_btnCheckoutTimer',[
        'order'=>$order])
    ?>

    <? else:?>
        <div class="order-form__title">Не выбран ни один заезд. Для выбора перейдите <a href="<?=$urlPre?>">сюда</a></div>
    <? endif;?>
</section>