<?php


/** @var yii\web\View $this */

use frontend\assets\BookingAsset;
use yii\helpers\Url;
BookingAsset::register($this);
$this->title = Yii::$app->name;
?>
<!--
<h1>Оформление заезда(ов)</h1>
-->
<a href="<?= Url::to(['index','step'=>1])?>" style="display: none"><--Перейти к выбору заездов</a>
<a href="<?= Url::to(['index','step'=>3])?>" style="display: none">Перейти к оплате--></a>
<section class="body">
    <div class="header next-step">
        <div class="header__wrapper">
            <a class="head-link prev" href="<?= Url::to(['index','step'=>1])?>">< Вернуться к выбору даты и времени</a>
            <div class="month noIcon">Оформление заказа</div>
            <a class="head-link" href="<?= Url::to(['index','step'=>3])?>">Перейти к оплате ></a>
        </div>
    </div>
    <div class="order-table">
        <div class="order-table__head">
            <div class="order-table__date">23.01.2024</div>
            <div class="order-table__time">12:25</div>
            <div class="order-table__type">Взрослый заезд</div>
            <div class="order-table__name"></div>
            <div class="order-table__col"></div>
            <div class="order-table__price">Цена</div>
            <div class="order-table__summa">Сумма</div>
            <div class="order-table__close"></div>
        </div>
        <div class="order-table__row">
            <div class="order-table__date"></div>
            <div class="order-table__time"></div>
            <div class="order-table__type"></div>
            <div class="order-table__name">SODi RT8</div>
            <div class="order-table__col">
                <div class="number">
                    <span class="minus" onclick="buttonMinus();">-</span>
                    <input type="text" id="inc" value="1"/>
                    <span class="plus" onclick="buttonPlus();">+</span>
                </div>
            </div>
            <div class="order-table__price">1000,00</div>
            <div class="order-table__summa">
                <span>3000,00</span>
                <div class="order-table__delete"></div>
            </div>
        </div>
        <div class="order-table__row">
            <div class="order-table__date"></div>
            <div class="order-table__time"></div>
            <div class="order-table__type"></div>
            <div class="order-table__name">SPORT 9</div>
            <div class="order-table__col">
                <div class="number">
                    <span class="minus" onclick="buttonMinus();">-</span>
                    <input type="text" id="inc" value="1"/>
                    <span class="plus" onclick="buttonPlus();">+</span>
                </div>
            </div>
            <div class="order-table__price">1000,00</div>
            <div class="order-table__summa">
                <span>3000,00</span>
                <div class="order-table__delete"></div>
            </div>
        </div>
    </div>
    <div class="order-table">
        <div class="order-table__head">
            <div class="order-table__date">23.01.2024</div>
            <div class="order-table__time">12:25</div>
            <div class="order-table__type">Взрослый заезд</div>
            <div class="order-table__name"></div>
            <div class="order-table__col"></div>
            <div class="order-table__price">Цена</div>
            <div class="order-table__summa">Сумма</div>
            <div class="order-table__close"></div>
        </div>
        <div class="order-table__row">
            <div class="order-table__date"></div>
            <div class="order-table__time"></div>
            <div class="order-table__type"></div>
            <div class="order-table__name">SODi RT8</div>
            <div class="order-table__col">
                <div class="number">
                    <span class="minus" onclick="buttonMinus();">-</span>
                    <input type="text" id="inc" value="1"/>
                    <span class="plus" onclick="buttonPlus();">+</span>
                </div>
            </div>
            <div class="order-table__price">1000,00</div>
            <div class="order-table__summa">
                <span>3000,00</span>
                <div class="order-table__delete"></div>
            </div>
        </div>
        <div class="order-table__row">
            <div class="order-table__date"></div>
            <div class="order-table__time"></div>
            <div class="order-table__type"></div>
            <div class="order-table__name">SPORT 9</div>
            <div class="order-table__col">
                <div class="number">
                    <span class="minus" onclick="buttonMinus();">-</span>
                    <input type="text" id="inc" value="1"/>
                    <span class="plus" onclick="buttonPlus();">+</span>
                </div>
            </div>
            <div class="order-table__price">1000,00</div>
            <div class="order-table__summa">
                <span>3000,00</span>
                <div class="order-table__delete"></div>
            </div>
        </div>
    </div>
    <div class="summ-block">
        ИТОГО: <div class="summ-block__result">9000</div> руб.
    </div>
    <div class="order-form__block">
        <div class="order-form__title">Данные для заказа</div>
        <form class="order-form">
            <div class="order-form__item">
                <label>Имя</label>
                <input type="text" name="name" placeholder="Имя">
            </div>
            <div class="order-form__item">
                <label>Фамилия</label>
                <input type="text" name="surname" placeholder="Фамилия">
            </div>
            <div class="order-form__item">
                <label>Телефон</label>
                <input type="text" name="phone" placeholder="+7(___) ___ -___-___*">
            </div>
            <div class="order-form__item">
                <label>Email</label>
                <input type="text" name="email" placeholder="Email">
            </div>
        </form>
    </div>
    <div class="timer-block">
        <div class="timer">
            <span class="timer-title">Оформить в течении</span>
            <div class="timer-time">10:00</div>
        </div>
    </div>
</section>