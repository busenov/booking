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
            <!--                <div class="modal-date">23.01.2024</div>-->
            <!--                <div class="modal-time">12:25</div>-->
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
                                                    </div>'
                        ]
                    )->textInput([
                        'data-min'=>0,
                        'data-max'=>14
                    ])->label($item->_carType->name . '(Цена: '.$item->_carType->getPriceBySlot($slot).' руб/машина)' )?>
                    <?php

                    ?>
                <?endforeach;?>
            <?endif;?>
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

JS;
$this->registerJs($js);

