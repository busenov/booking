<?php

/* @var $this yii\web\View */
/* @var $user User */

use booking\entities\User\User;

$confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['auth/signup/confirm', 'token' => $user->email_confirm_token]);
?>
    Добрый день <?= $user->shortName ?>,

    Для подтверждения регистрации перейдите по ссылке:

<?= $confirmLink ?>