<?php


/** @var yii\web\View $this */

use frontend\assets\BookingAsset;
use yii\helpers\Url;
BookingAsset::register($this);
$this->title = Yii::$app->name;
?>
<!--
<h1>Выбор заезда</h1>
-->
<a href="<?= Url::to(['index','step'=>2])?>" style="display: none">Перейти к оформлению--></a>

<section class="body">
    <div class="header">
        <div class="month">Февраль 2024</div>
        <a class="head-link" href="<?= Url::to(['index','step'=>2])?>">Перейти к оформлению ></a>
    </div>
    <div class="week">
        <div class="day past">
            <div class="day-name">ПН</div>
            <div class="day-date">
                <div class="day-date__count">29</div>
                <div class="day-date__label">100</div>
            </div>
        </div>
        <div class="day past">
            <div class="day-name">ВТ</div>
            <div class="day-date">
                <div class="day-date__count">30</div>
                <div class="day-date__label">100</div>
            </div>
        </div>
        <div class="day past">
            <div class="day-name">СР</div>
            <div class="day-date">
                <div class="day-date__count">31</div>
                <div class="day-date__label">100</div>
            </div>
        </div>
        <div class="day dostupno">
            <div class="day-name">ЧТ</div>
            <div class="day-date">
                <div class="day-date__count">1</div>
                <div class="day-date__label">140</div>
            </div>
        </div>
        <div class="day malo">
            <div class="day-name">ПТ</div>
            <div class="day-date">
                <div class="day-date__count">2</div>
                <div class="day-date__label">20</div>
            </div>
        </div>
        <div class="day zabronirovano">
            <div class="day-name red">СБ</div>
            <div class="day-date">
                <div class="day-date__count">3</div>
                <div class="day-date__label">20</div>
            </div>
        </div>
        <div class="day noActive">
            <div class="day-name red">ВС</div>
            <div class="day-date">
                <div class="day-date__count">4</div>
                <div class="day-date__label">20</div>
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
    <div class="step-title">Заезды 29 января 2024г.</div>
    <form class="form">
        <div class="for-childs">
            <img src="/booking/img/child.png" class="child-img">
            <span class="form-text">Показать только детские заезды:</span>
            <label class="switch">
                <input type="checkbox">
                <span class="slider round"></span>
            </label>
        </div>
        <div class="clubs-enter">
            <img src="/booking/img/star.png" class="child-img">
            <span class="form-text">Показать только детские заезды:</span>
            <label class="switch">
                <input type="checkbox">
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