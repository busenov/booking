<?php

use booking\entities\Car\CarType;
use booking\entities\Slot\Slot;
use booking\forms\manage\Car\CarTypeForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var CarTypeForm $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="car-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'qty')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList(CarType::getStatusList(), ['prompt' => '']) ?>

    <?= $form->field($model, 'type')->dropDownList(Slot::getTypeList(), ['prompt' => '']) ?>

    <?= $form->field($model, 'pwr')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>

    <br>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
