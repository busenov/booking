<?php

use booking\entities\Car\CarType;
use booking\entities\Schedule\Schedule;
use booking\forms\manage\Car\CarTypeForm;
use booking\forms\manage\Schedule\ScheduleForm;
use kartik\datecontrol\DateControl;
use kartik\widgets\TouchSpin;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var ScheduleForm $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="schedule-form">

    <?php $form = ActiveForm::begin(); ?>

    <?if ($model->weekday > 0) :?>
    <?= $form->field($model, 'weekday')->dropDownList(Schedule::getWeekdaysList(), ['prompt' => '']) ?>
    <?else :?>
    <?= $form->field($model, 'weekday')->hiddenInput(['maxlength' => true])->label(false) ?>
    <? endif;?>

    <div class="row">
        <div class="col-md-3">
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
        </div>
        <div class="col-md-3">
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
        </div>
        <div class="col-md-3">
            <?=$form->field($model, 'duration_min')->widget(TouchSpin::class, [
                'options'=>['placeholder'=>''],
                'pluginOptions' => [
                    'step' => 5,
                ]
            ]);
            ?>
        </div>
        <div class="col-md-3">
            <?=$form->field($model, 'interval_min')->widget(TouchSpin::class, [
                'options'=>['placeholder'=>''],
                'pluginOptions' => [
                    'step' => 5,
                ]
            ]);
            ?>
        </div>
    </div>

    <?= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>

    <br>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
