<?php

use backend\assets\CalendarAsset;
use booking\entities\Car\CarType;
use booking\forms\manage\Order\OrderCreateForm;
use booking\helpers\AppHelper;
use booking\helpers\CarHelper;
use booking\helpers\DateHelper;
use booking\useCases\manage\CarTypeManageService;
use booking\useCases\manage\ScheduleManageService;
use kartik\widgets\TouchSpin;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var array $calendar */
/** @var OrderCreateForm $model */

$this->title = "Календарь";
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
CalendarAsset::register($this);
//dump(array_key_exists('selected_day_unx',$_COOKIE));exit;
$selected_day=(array_key_exists('selected_day_unx',$_COOKIE)AND(is_int($_COOKIE['selected_day_unx'])))?$_COOKIE['selected_day_unx']:time();
?>
<pre>
    <?php
    dump($calendar);
    dump(Yii::$app->request->getCookies()->get('selected_day'));
//    dump($_COOKIE['selected_day']);
//    dump($_COOKIE['selected_day_unx']);
//    dump(strtotime($_COOKIE['selected_day']));
//    dump(json_encode($calendar));
    ?>
</pre>
<script type='text/javascript'>
    var $ykv_calendar=<?=json_encode($calendar)?>;
    console.log($ykv_calendar);
</script>
<div class="calendar">

    <h1><?= Html::encode($this->title) ?></h1>

    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 text-center mb-5">
                    <h2 class="heading-section"></h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="elegant-calencar d-md-flex">
                        <div class="wrap-header d-flex align-items-center">
                            <p id="reset">сброс</p>
                            <div id="header" class="p-0">
                                <div class="pre-button d-flex align-items-center justify-content-center"><i class="fa fa-chevron-left"></i></div>
                                <div class="head-info">
                                    <div class="head-day"></div>
                                    <div class="head-month"></div>
                                    <div class="head-slots">

                                    </div>

                                    <div class="head-order">

                                    </div>
                                    <div class="head-button align-bottom">
                                        <div class=" position-relative">
                                            <div class="btn-group  position-absolute end-0">
                                                <a href="#"
                                                   id="generate-btn"
                                                   class="btn btn btn-success"
                                                   data-url="<?=Url::to(['slot/generate-ajax'])?>"
                                                   data-unixTime="<?=time()?>"
                                                >Сгенерировать слоты</a>
<!--                                                <a href="--><?php //=Url::to(['create','weekday'=>Schedule::WEEKDAY_WEEKEND])?><!--" class="btn btn-primary">Создать расписание на выходные</a>-->
                                                <a href="#"
                                                   id="clear-btn"
                                                   class="btn btn-danger"
                                                   data-url="<?=Url::to(['slot/clear-ajax'])?>"
                                                   data-unixTime="<?=time()?>"
                                                >Очистить</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="next-button d-flex align-items-center justify-content-center"><i class="fa fa-chevron-right"></i></div>
                            </div>
                        </div>
                        <div class="calendar-wrap">
                            <table id="calendar">
                                <thead>
                                <tr>
                                    <th>Пн</th>
                                    <th>Вт</th>
                                    <th>Ср</th>
                                    <th>Чт</th>
                                    <th>Пт</th>
                                    <th>Сб</th>
                                    <th>Вс</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php $form = ActiveForm::begin(); ?>
            <div class="row step2">
                <div class="col-md-12">
                    <div class="">
                        <h2 id="step2-title"><?= AppHelper::datetimeFormat($selected_day,false)?></h2>
                        <h3>Выберите время</h3>

                        <div id="step2-times">
                            <?
                            $year=date('Y',$selected_day);
                            $day=date('j',$selected_day)
                            ?>
                            <?if (($calendar[$year]) && (isset($calendar[$year][$day]))):?>
                                <?foreach ($calendar[$year][$day] as $slotId => $slot) :?>
                                    <!--                                            --><?//dump(is_int($slotId));exit;?>
                                    <?if (is_int($slotId)):?>
                                        <button class="btn <?=$slot['isChild']?'btn-warning':'btn-success'?> small btn-slot" type="button" data-slot_id="<?=$slotId?>"><?= DateHelper::timeIntToStr($slot['begin'],false)?> <?=$slot['qty']?>(<?=$slot['free']?>)</button>
<!--                                        <a href="#" class="btn btn-success small btn-slot" data-slotId="--><?php //=$slotId?><!--">--><?php //= DateHelper::timeIntToStr($slot['begin'],false)?><!-- --><?php //=$slot['qty']?><!--(--><?php //=$slot['free']?><!--)</a>-->
                                    <?endif;?>
                                <?endforeach;?>
                            <?endif;?>
                        </div>
                        <div>

                        </div>
                    </div>
                    <div>
                        <?= $form->field($model, 'slot_id')->hiddenInput(['id'=>'form-slot_id'])->label(false) ?>
                        <?= $form->field($model->customer, 'name')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model->customer, 'telephone')->textInput(['maxlength' => true]) ?>
                        <?if ($model->items):?>
                            <?php foreach ($model->items as $i => $item) :?>
                                <?=$form->field($item, '[' . $i . ']qty')->widget(TouchSpin::class, [
                                    'options'=>[
                                        'placeholder'=>'',
                                        'class'=>'form_cars'
                                    ],
                                    'pluginOptions' => [
                                        'step' => 1,
                                    ]
                                ])->label($item->_carType->name);
                                ?>
                            <?endforeach;?>
                        <?endif;?>
                        <div class="form-group">
                            <?= Html::submitButton('Забронировать', ['class' => 'btn btn-success']) ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </section>
</div>
