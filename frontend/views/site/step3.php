<?php


/** @var yii\web\View $this */

use frontend\assets\BookingAsset;
use yii\helpers\Url;
BookingAsset::register($this);
$this->title = Yii::$app->name;
?>

<!--
<h1>Оплата</h1>
-->
<a href="<?= Url::to(['index','step'=>2])?>" style="display: none"><--Перейти к оформлению заездов</a>
<a href="<?= Url::to(['index','step'=>4])?>" style="display: none">Перейти к вводу доп. информации--></a>

<section class="body">
    <div class="header next-step">
        <div class="header__wrapper">
            <a class="head-link prev" href="<?= Url::to(['index','step'=>2])?>">< Заказать заезд</a>
            <div class="month noIcon">Оплата успешно прошла!</div>
        </div>
    </div>
    <div class="order-result__block">
        <div class="order-result__text">Номер брони: <div class="order-result__number">123321</div>. Для подтверждения с вами свяжутся наши менеджеры.</div>
        <div class="order-result__text">Заполните дополнительную информацию о заезде:</div>
    </div>
    <div class="pay-table">
        <div class="pay-table__head">
            <div class="pay-table__date">23.01.2024</div>
            <div class="pay-table__time">12:25</div>
            <div class="pay-table__name">Взрослый заезд</div>
            <img src="/booking/img/cart.png" class="pay-table__icon">
        </div>
        <form class="pay-table__form">
            <div class="pay-table__row">
                <label>Гонщик 1:</label>
                <input type="text" name="name">
                <input type="text" name="weight" placeholder="Вес">
                <input type="text" name="height" placeholder="Рост">
                <input type="text" name="date" placeholder="Дата рождения">
            </div>
            <div class="pay-table__row">
                <label>Гонщик 2:</label>
                <input type="text" name="name">
                <input type="text" name="weight" placeholder="Вес">
                <input type="text" name="height" placeholder="Рост">
                <input type="text" name="date" placeholder="Дата рождения">
            </div>
            <div class="pay-table__row">
                <label>Гонщик 3:</label>
                <input type="text" name="name">
                <input type="text" name="weight" placeholder="Вес">
                <input type="text" name="height" placeholder="Рост">
                <input type="text" name="date" placeholder="Дата рождения">
            </div>
            <div class="pay-table__row">
                <label>Гонщик 4:</label>
                <input type="text" name="name">
                <input type="text" name="weight" placeholder="Вес">
                <input type="text" name="height" placeholder="Рост">
                <input type="text" name="date" placeholder="Дата рождения">
            </div>
            <button class="pay-table__btn">Копировать данные на все заезды</button>
        </form>
    </div>

    <div class="pay-table">
        <div class="pay-table__head">
            <div class="pay-table__date">23.01.2024</div>
            <div class="pay-table__time">12:25</div>
            <div class="pay-table__name">Детский заезд</div>
            <img src="/booking/img/cart-white.png" class="pay-table__icon">
        </div>
        <form class="pay-table__form">
            <div class="pay-table__row">
                <label>Гонщик 1:</label>
                <input type="text" name="name">
                <input type="text" name="weight" placeholder="Вес">
                <input type="text" name="height" placeholder="Рост">
                <input type="text" name="date" placeholder="Дата рождения">
            </div>
            <div class="pay-table__row">
                <label>Гонщик 2:</label>
                <input type="text" name="name">
                <input type="text" name="weight" placeholder="Вес">
                <input type="text" name="height" placeholder="Рост">
                <input type="text" name="date" placeholder="Дата рождения">
            </div>
            <div class="pay-table__row">
                <label>Гонщик 3:</label>
                <input type="text" name="name">
                <input type="text" name="weight" placeholder="Вес">
                <input type="text" name="height" placeholder="Рост">
                <input type="text" name="date" placeholder="Дата рождения">
            </div>
            <div class="pay-table__row">
                <label>Гонщик 4:</label>
                <input type="text" name="name">
                <input type="text" name="weight" placeholder="Вес">
                <input type="text" name="height" placeholder="Рост">
                <input type="text" name="date" placeholder="Дата рождения">
            </div>
            <button class="pay-table__btn">Копировать данные на все заезды</button>
        </form>
    </div>

    <div class="save-block">
        <button class="save-block__btn">Сохранить</button>
    </div>
</section