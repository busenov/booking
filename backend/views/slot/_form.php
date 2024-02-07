<?php

use booking\entities\Slot\Slot;
use kartik\datecontrol\DateControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var \booking\forms\manage\Slot\SlotForm $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="slot-form">

    <?php $form = ActiveForm::begin(); ?>

<!--    --><?php //= $form->field($model, 'date')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'date')->widget(DateControl::class, [
        'type'=>DateControl::FORMAT_DATE,
        'ajaxConversion'=>true,
        'widgetOptions' => [
            'removeButton' => false,

            'options'=>[
                'class'=>'slot-date',
            ],
            'pluginOptions' => [
                'autoclose' => true,
                'todayBtn'=>true,
                'todayHighlight'=>true
            ]
        ]
    ]);
    ?>

    <?= $form->field($model, 'begin')->widget(DateControl::class, [
        'type'=>DateControl::FORMAT_TIME,
        'displayFormat' => 'php:H:i',
        'displayTimezone'=>'GMT',
        'saveTimezone'=>'GMT',
        'ajaxConversion'=>true,
        'widgetOptions' => [
            'options'=>[
                'class'=>'slot-time',
            ],
            'pluginOptions' => [
                'autoclose' => true,
                'minuteStep' => 5,
            ]
        ]
    ]);
    ?>

    <?= $form->field($model, 'end')->widget(DateControl::class, [
        'type'=>DateControl::FORMAT_TIME,
        'displayFormat' => 'php:H:i',
        'displayTimezone'=>'GMT',
        'saveTimezone'=>'GMT',
        'ajaxConversion'=>true,
        'widgetOptions' => [
            'options'=>[
                'class'=>'slot-time',
            ],
            'pluginOptions' => [
                'autoclose' => true,
                'minuteStep' => 5,
            ]
        ]
    ]);
    ?>
    <?= $form->field($model, 'qty')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList(Slot::getStatusList(), ['prompt' => '']) ?>

    <?= $form->field($model, 'status')->radioList(Slot::getTypeList(), ['prompt' => '']) ?>

    <?= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>

    <br>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$js = <<<JS
$(".slot-time").on('changeTime.timepicker', function(e) {

    // var hours = e.time.hours;
    // var minutes = e.time.minutes;
    //
    // if (hours > 18) {
    //     $(this).timepicker('setTime', '7:30');
    // }
    //
    // if (hours < 7) {
    //     $(this).timepicker('setTime', '18:30');
    // }
    //
    // if (hours == 7 && minutes < 30 ) {
    //     $(this).timepicker('setTime', '18:30');
    // }
    //
    // if (hours == 18 && minutes > 30 ) {
    //     $(this).timepicker('setTime', '7:30');
    // }
});
JS;
//$this->registerJs($js);
?>