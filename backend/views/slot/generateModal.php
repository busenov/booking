<?php

use booking\entities\Car\CarType;
use booking\entities\Schedule\Schedule;
use booking\forms\manage\Car\CarTypeForm;
use booking\forms\manage\Schedule\ScheduleForm;
use booking\forms\manage\Slot\GenerateForm;
use kartik\datecontrol\DateControl;
use kartik\widgets\TouchSpin;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var GenerateForm $model */
/** @var yii\widgets\ActiveForm $form */
/** @var int $unixTime      текущий день*/
?>


<div class="modal fade" id="generateSlots" tabindex="-1" aria-labelledby="generateModalLabel" aria-hidden="true">
    <?php $form = ActiveForm::begin([
        'action' => ['slot/generate-slots','unixTime'=>$unixTime],
    ]); ?>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Генерация заездов</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($model, 'scheduleId')->dropDownList($model->getScheduleList(), [
                            'prompt' => '',
                            'id'=>'generateModal-scheduleId'
                        ]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($model, 'activateSlot')->checkbox([]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'begin')->widget(DateControl::class, [
                            'type'=>DateControl::FORMAT_TIME,
                            'displayFormat' => 'php:H:i',
                            'displayTimezone'=>'GMT',
                            'saveTimezone'=>'GMT',
                            'ajaxConversion'=>true,
                            'widgetOptions' => [
                                'options'=>[
                                    'class'=>'slot-time',
                                    'id'=>'generateModal-begin'
                                ],
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'minuteStep' => 5,
                                ]
                            ]
                        ]);
                        ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'end')->widget(DateControl::class, [
                            'type'=>DateControl::FORMAT_TIME,
                            'displayFormat' => 'php:H:i',
                            'displayTimezone'=>'GMT',
                            'saveTimezone'=>'GMT',
                            'ajaxConversion'=>true,
                            'widgetOptions' => [
                                'options'=>[
                                    'class'=>'slot-time',
                                    'id'=>'generateModal-end'
                                ],
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'minuteStep' => 5,
                                ]
                            ]
                        ]);
                        ?>
                    </div>
                    <div class="col-md-6">
                        <?=$form->field($model, 'duration_min')->widget(TouchSpin::class, [
                            'options'=>[
                                'placeholder'=>'',
                                'id'=>'generateModal-duration_min'
                            ],
                            'pluginOptions' => [
                                'step' => 5,
                            ]
                        ]);
                        ?>
                    </div>
                    <div class="col-md-6">
                        <?=$form->field($model, 'interval_min')->widget(TouchSpin::class, [
                            'options'=>[
                                'placeholder'=>'',
                                'id'=>'generateModal-interval_min'
                            ],
                            'pluginOptions' => [
                                'step' => 5,
                            ]
                        ]);
                        ?>
                    </div>
                </div>
                <?= $form->field($model, 'note')->textInput([
                    'maxlength' => true,
                    'id'=>'generateModal-note'
                ]) ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <?= Html::submitButton('Сгенерировать', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?
$schedules=[];
foreach ($model->getSchedules() as $schedule) {
    $schedules[$schedule->id]=[
        'name'=>$schedule->getName(),
        'begin_str'=>$schedule->begin_str,
        'end_str'=>$schedule->end_str,
        'duration_min'=>$schedule->duration_min,
        'interval_min'=>$schedule->interval_min,
        'note'=>$schedule->note,
    ];
}
$schedules_json=json_encode($schedules);
$js = <<<JS
    var generateModal_schedules=$schedules_json
    $("document").ready(function(){
        console.log(generateModal_schedules);
        $("#generateModal-scheduleId").change(function (e){
            console.log(this.value);
            if (generateModal_schedules[this.value]) {
                let schedule;
                schedule=generateModal_schedules[this.value]
                console.log(schedule['begin']);
                $("#generateform-begin-disp").val(schedule['begin_str'])
                $("#generateform-end-disp").val(schedule['end_str'])
                $("#generateModal-duration_min").val(schedule['duration_min'])
                $("#generateModal-interval_min").val(schedule['interval_min'])
            }
            
        })
    });
JS;
$this->registerJs($js);
