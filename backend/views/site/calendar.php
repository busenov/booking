<?php

use backend\assets\CalendarAsset;
use booking\entities\Car\CarType;
use booking\helpers\CarHelper;
use booking\useCases\manage\CarTypeManageService;
use booking\useCases\manage\ScheduleManageService;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var array $calendar */

$this->title = "Календарь";
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
CalendarAsset::register($this);
?>
<pre>
    <?php
//    dump($calendar);
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
                                    <div class="head-slots"></div>
                                    <div class="head-month"></div>
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
        </div>
    </section>
</div>
