<?php

use booking\entities\Order\Order;
use booking\entities\Slot\Slot;
use booking\forms\manage\Order\SlotCreateForm;
use kartik\widgets\TouchSpin;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var SlotCreateForm $orderForm */
/** @var Order $order */
/** @var Slot $slot */

?>

<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
                            <div class="modal-date"><?=$slot->getName()?></div>
            <div class="modal-name" id="modal-title" ></div>
        </div>
        <div class="modal-body">

            <?php $form = ActiveForm::begin([
                'class'=>"modal-form",
                'method'=>"post",
                'id'=> 'modal-form',
                'action'=>Url::to(['site/add-to-order-ajax','slot_id'=>$slot->id])
            ]); ?>
            <?= $form->field($orderForm, 'slot_id')->hiddenInput(['id'=>'modal-form-slot_id'])->label(false) ?>
            <?if ($orderForm->items):?>
                <?php foreach ($orderForm->items as $i => $item) :?>
                    <?=$form->field($item, '[' . $i . ']qty',
                        [
                            'template' => '{label}  <div class="input-group mb-3">
                                                        <button class="btn btn-outline-secondary btn-inc" type="button" data-inc="-" onclick="btnInc(this)">-</button>
                                                        {input}{error}{hint}
                                                        <button class="btn btn-outline-secondary btn-inc" type="button" data-inc="+" onclick="btnInc(this)">+</button>
                                                    </div>',

                        ]
                    )->textInput([
                        'data-min'=>0,
                        'data-max'=>14,
                        'class'=>'modal-form_car form-control',
                        'data-price'=>$item->_carType->getPriceBySlot($slot)
                    ])->label($item->_carType->name . '(Цена: '.$item->_carType->getPriceBySlot($slot).' руб/машина)' )?>
                    <?php

                    ?>
                <?endforeach;?>
            <?endif;?>
            <div class="row">
                <div class="col-md-6">
                    <b>ИТОГО:</b>
                </div>
                <div class="col-md-6 d-flex justify-content-end">

                <span id="modal-total-price"><?=number_format($order->totalBySlot($slot->id),0,'',' ')?></span>
                <b>&nbsp;руб</b>
                </div>
            </div>

            <div class="modal-form__row">
                <?= Html::submitButton('Добавить в заказ', ['class' => 'add-in-zakaz','onClick'=>'$ykv_toIssue=false']) ?>
                <?= Html::submitButton('Добавить и перейти к оформлению', ['class' => 'add-issue','onClick'=>'$ykv_toIssue=true']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$js = <<<JS
    let 
        modalFormCars=document.getElementsByClassName('modal-form_car');
        modalTotalPrice=document.getElementById('modal-total-price');
        console.log('tut');
    for (let i = 0; i < modalFormCars.length; i++) {
        
        modalFormCars[i].addEventListener('change', function () {change();});
    }
    function change(){
        let total=0;
        for (let i = 0; i < modalFormCars.length; i++) {
            total += modalFormCars[i].value * modalFormCars[i].dataset.price 
        }
        modalTotalPrice.innerHTML = ''+total;
    }

JS;
$this->registerJs($js);

