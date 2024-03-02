<?php


/** @var yii\web\View $this */
/** @var array $calendar */
/** @var Order $order */
/** @var RacersForm $racersForm */

use booking\entities\Order\Order;
use booking\forms\manage\Order\RacersForm;
use frontend\assets\BookingAsset;
use kartik\datecontrol\DateControl;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Url;
use yii\web\YiiAsset;

YiiAsset::register($this);
BookingAsset::register($this);
$this->title = Yii::$app->name;

$urlPre=Url::to(['index','step'=>1]);
$urlNxt='';
?>
<script type='text/javascript'>
    var $ykv_step=4;
</script>
<section class="body">
    <div class="header next-step">
        <div class="header__wrapper">
            <a class="head-link prev" href="<?= $urlPre ?>">< Заказать новый заезд</a>
            <div class="month noIcon">Оплата успешно прошла!</div>
        </div>
    </div>
    <div class="order-result__block">
        <div class="order-result__text">Номер брони: <div class="order-result__number"><?=$order->id?></div>. Для подтверждения с вами свяжутся наши менеджеры.</div>
        <div class="order-result__text">Заполните дополнительную информацию о заезде:</div>
    </div>
    <? if ($order->items):?>
        <?php $form = ActiveForm::begin([
            'options'=>[
                'class'=>"pay-table__form",
            ],
            'method'=>"post",
            'action'=>Url::to(['index','step'=>4])
        ]); ?>
        <?=$form->field($racersForm,'order_id')->hiddenInput()->label(false)?>
        <?php
        $currentSlotId=null;

        ?>
        <? foreach ($racersForm->items as $i=>$item) :?>

            <? if ($currentSlotId!==$item->slot_id):?>
                <?if (!empty($currentSlotId)) :?>
            <button class="pay-table__btn" type="button" data-slot_id="<?=$currentSlotId?>">Копировать данные на все заезды</button>
    </div>
                <? endif;?>
    <div class="pay-table">
        <div class="pay-table__head">
            <div class="pay-table__date"><?=$item->slot->getDateStr()?></div>
            <div class="pay-table__time"><?=$item->slot->getTimeStr()?></div>
            <div class="pay-table__name"><?=$item->slot->getTypeNameStr()?></div>
            <img src="/booking/img/cart.png" class="pay-table__icon">
        </div>
                <?php
                    $currentSlotId=$item->slot_id;
                    $numRacer=1;
                ?>
            <?endif;?>

        <div class="pay-table__row" data-slot_id="<?=$currentSlotId?>">
            <label>Гонщик <?=$numRacer++?>:</label>
            <?=$form->field($item,'[' . $i . ']name')->textInput(['placeholder'=>'Имя(ник)','class'=>'racer_name'])->label(false)?>
            <?=$form->field($item,'[' . $i . ']weight')->textInput(['placeholder'=>'Вес','class'=>'racer_weight'])->label(false)?>
            <?=$form->field($item,'[' . $i . ']height')->textInput(['placeholder'=>'Рост','class'=>'racer_height'])->label(false)?>

            <?=$form->field($item, '[' . $i . ']birthday')->widget(DateControl::class, [
                'type'=>DateControl::FORMAT_DATE,
                'ajaxConversion'=>false,
                'widgetOptions' => [
                    'pluginOptions' => [
                        'autoclose' => true,

                    ]
                ]
            ])->label(false);
            ?>

            <?=$form->field($item,'[' . $i . ']slot_id')->hiddenInput([])->label(false)?>
        </div>
        <?endforeach;?>
        <?php ActiveForm::end(); ?>
        <button class="pay-table__btn" data-slot_id="<?=$currentSlotId?>">Копировать данные на все заезды</button>
    <?endif;?>
    </div>

    <div class="save-block">
        <button class="save-block__btn" id="save-block__btn">Сохранить</button>
    </div>
</section>