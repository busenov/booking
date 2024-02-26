<?php

use booking\entities\User\User;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user User */
/* @var $resetLink string */

?>
<div class="password-reset">
    <p>Добрый день <?= Html::encode($user->shortName) ?>,</p>

    <p>Для сброса пароля перейдите по ссылке:</p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>