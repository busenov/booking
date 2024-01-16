<?php
use common\widgets\Alert;
use himiklab\yii2\recaptcha\ReCaptcha3;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap5\ActiveForm */
/* @var $model \booking\forms\auth\AdminSignupForm */

$this->title = 'Регистрация';

$fieldName = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-user form-control-feedback'></span>"
];

$fieldEmail = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-envelope form-control-feedback'></span>"
];

$fieldNameClient = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-tower form-control-feedback'></span>"
];

$fieldPassword = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];

$fieldRepeatPassword = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-log-in form-control-feedback'></span>"
];
//
//$fieldOptions2 = [
//    'options' => ['class' => 'form-group has-feedback'],
//    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
//];
?>

<div class="register-box">
    <div class="register-logo">
        <a href="/admin/"><b>Rent4b.</b>ЛК</a>
    </div>

    <div class="register-box-body">
        <p class="login-box-msg">Регистрация нового пользователя</p>

        <?= Alert::widget() ?>

        <?php $form = ActiveForm::begin(['id' => 'signup-form', 'enableClientValidation' => false]); ?>

        <?= $form
            ->field($model, 'name', $fieldName)
            ->label(false)
            ->textInput(['placeholder' => $model->getAttributeLabel('name')]) ?>

        <?= $form
            ->field($model->client, 'name', $fieldNameClient)
            ->label(false)
            ->textInput(['placeholder' => $model->client->getAttributeLabel('name')]) ?>

        <?= $form
            ->field($model, 'email', $fieldEmail)
            ->label(false)
            ->textInput(['placeholder' => $model->getAttributeLabel('email')]) ?>

        <?= $form
            ->field($model, 'password', $fieldPassword)
            ->label(false)
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>

        <?= $form
            ->field($model, 'password_repeat', $fieldRepeatPassword)
            ->label(false)
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password_repeat')]) ?>

        <?= (Yii::$app->settings->reCaptcha->google_secretV3 and YII_ENV_PROD)?$form->field($model, 'reCaptcha')->widget(
            ReCaptcha3::class,
            [
                'siteKey' => Yii::$app->settings->reCaptcha->google_siteKeyV3, // unnecessary is reCaptcha component was set up
                'action' => 'signup',
            ]
        )->label(false):'' ?>

            <div class="row">
                <div class="col-xs-8">
                    <?= $form->field($model, 'agreeTerm')->checkbox()->label('Я согласен с '.Html::a('условиями',['/term'])) ?>
                </div>
                <!-- /.col -->
                <div class="col-xs-4" style="padding-left: 0px;">
                    <?= Html::submitButton('Регистрация', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
                </div>
                <!-- /.col -->
            </div>

        <?php ActiveForm::end(); ?>

<!--        <div class="social-auth-links text-center">-->
<!--            <p>- OR -</p>-->
<!--            <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign up using-->
<!--                Facebook</a>-->
<!--            <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign up using-->
<!--                Google+</a>-->
<!--        </div>-->

        <?=Html::a('Я уже зарегистрирован',['auth/auth/login'],['class'=>"text-center"])?>
    </div>
    <!-- /.form-box -->
</div>
