<?php
use booking\forms\manage\Order\OrderCreateForm;
use frontend\assets\BookingAsset;
use yii\helpers\Url;
use yii\web\YiiAsset;

/** @var yii\web\View $this */
/** @var array $calendar */
/** @var OrderCreateForm $model */

YiiAsset::register($this);
BookingAsset::register($this);
$this->title = Yii::$app->name;

$selected_day=(array_key_exists('selected_day_unx',$_COOKIE)AND(is_int($_COOKIE['selected_day_unx'])))?$_COOKIE['selected_day_unx']:time();
$onlyChildren= ((array_key_exists('onlyChildren',$_COOKIE))AND($_COOKIE['onlyChildren']==='true'));
$clubRaces= ((array_key_exists('clubRaces',$_COOKIE))AND($_COOKIE['clubRaces']==='true'));
?>
<script type='text/javascript'>
    var $ykv_calendar=<?=json_encode($calendar)?>;
    console.log($ykv_calendar);
</script>

<a href="<?= Url::to(['index','step'=>2])?>" style="display: none">Перейти к оформлению--></a>

<section class="body">
    <div class="header">
        <div class="header__wrapper">
            <div class="month">Февраль 2024</div>
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
            <div class="week__day red">СБ</div>
            <div class="week__day red">ВС</div>
        </div>
        <div class="week__dates">
            <div class="week__date past"
                 data-wday="1"
                 data-day="<?=$calendar[1]['unixTime']?>"
            >
                <div class="week__date-count">29</div>
                <div class="week__date-label">100</div>
            </div>
            <div class="week__date past"
                 data-wday="2"
                 data-day="<?=$calendar[2]['unixTime']?>"
            >
                <div class="week__date-count">30</div>
                <div class="week__date-label">100</div>
            </div>
            <div class="week__date past"
                 data-wday="3"
                 data-day="<?=$calendar[3]['unixTime']?>"
            >
                <div class="week__date-count">31</div>
                <div class="week__date-label">100</div>
            </div>
            <div class="week__date current"
                 data-wday="4"
                 data-day="<?=$calendar[4]['unixTime']?>"
            >
                <div class="week__date-count">1</div>
                <div class="week__date-label dostupno">140</div>
            </div>
            <div class="week__date isActive"
                 data-wday="5"
                 data-day="<?=$calendar[5]['unixTime']?>"
            >
                <div class="week__date-count">2</div>
                <div class="week__date-label malo">20</div>
            </div>
            <div class="week__date"
                 data-wday="6"
                 data-day="<?=$calendar[6]['unixTime']?>"
            >
                <div class="week__date-count">3</div>
                <div class="week__date-label busy">20</div>
            </div>
            <div class="week__date noActive"
                 data-wday="7"
                 data-day="<?=$calendar[7]['unixTime']?>"
            >
                <div class="week__date-count">4</div>
                <div class="week__date-label">20</div>
            </div>
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
    <div class="step-title">Заезды <span id="race-day">29 января 2024</span>г.</div>
    <form class="form">
        <div class="for-childs">
            <img src="/booking/img/child.png" class="child-img">
            <span class="form-text">Показать только детские заезды:</span>
            <label class="switch">
                <input type="checkbox" id="only-children" <?=$onlyChildren?'checked':''?>>
                <span class="slider round"></span>
            </label>
        </div>
        <div class="clubs-enter">
            <img src="/booking/img/star.png" class="child-img">
            <span class="form-text">Показать только клубные заезды:</span>
            <label class="switch">
                <input type="checkbox" id="club-races" <?=$clubRaces?'checked':''?>>
                <span class="slider round"></span>
            </label>
        </div>
        <div class="nomer-prav">
            <label for="prava">Номер прав:</label>
            <input type="text" class="prava">
            <button>Проверить</button>
        </div>
    </form>
    <div class="result-table">
        <div class="result-table__time-row">12:00</div>
        <div class="result-table__row">
            <div class="result-table__info-block">
                <div class="result-table__time">12:00 - 12:15</div>
                <div class="result-table__info">Свободно: 10 мест</div>
            </div>
            <button class="result-table__btn btn" data-bs-toggle="modal" data-bs-target="#exampleModal">Забронировать</button>
        </div>
        <div class="result-table__row">
            <div class="result-table__info-block">
                <div class="result-table__time">12:15 - 12:30</div>
                <div class="result-table__info purple"><img src="/booking/img/child.png" class="result-table__icon"> Свободно: 10 мест</div>
            </div>
            <button class="result-table__btn btn" data-bs-toggle="modal" data-bs-target="#exampleModal">Забронировать</button>
        </div>
        <div class="result-table__time-row">13:00</div>
        <div class="result-table__row">
            <div class="result-table__info-block">
                <div class="result-table__time">13:00 - 13:15</div>
                <div class="result-table__info yellow"><img src="/booking/img/star.png" class="result-table__icon"> Свободно: 10 мест</div>
            </div>
            <button class="result-table__btn btn" data-bs-toggle="modal" data-bs-target="#exampleModal">Забронировать</button>
        </div>
        <div class="result-table__row">
            <div class="result-table__info-block">
                <div class="result-table__time">13:15 - 13:30</div>
                <div class="result-table__info">Свободно: 10 мест</div>
            </div>
            <button class="result-table__btn btn" data-bs-toggle="modal" data-bs-target="#exampleModal">Забронировать</button>
        </div>
        <div class="result-table__row">
            <div class="result-table__info-block noActive">
                <div class="result-table__time">13:30 - 13:45</div>
                <div class="result-table__info">Свободно: 10 мест</div>
            </div>
            <button class="result-table__btn btn" data-bs-toggle="modal" data-bs-target="#exampleModal">Забронировать</button>
        </div>
    </div>
    <div class="timer-block">
        <div class="timer">
            <span class="timer-title">Оформить в течении</span>
            <div class="timer-time">10:00</div>
        </div>
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-date">23.01.2024</div>
                <div class="modal-time">12:25</div>
                <div class="modal-name">Взрослый заезд</div>
            </div>
            <div class="modal-body">
                <form class="modal-form">
                    <div class="modal-form__row">
                        <label>SODi RT8:</label>
                        <input type="text">
                    </div>
                    <div class="modal-form__row">
                        <label>SPORT 9:</label>
                        <input type="text">
                    </div>
                    <div class="modal-form__row">
                        <button class="add-in-zakaz">Добавить в заказ</button>
                        <button class="add-issue">Добавить и оформить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>