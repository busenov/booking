<?php

/* @var $this yii\web\View */
/* @var $user User */
/* @var $resetLink string */

use booking\entities\User\User;

?>
    Добрый день <?= $user->shortName ?>,

    Для сброса пароля перейдите по ссылке:

<?= $resetLink ?>