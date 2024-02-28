<?php

use booking\forms\AmoCRM\CredentialForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var CredentialForm $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="companies-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'domain')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'token')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'refresh_token')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'expires')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'client_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'client_secret')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'redirect_uri')->textInput(['maxlength' => true]) ?>

    <br>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
