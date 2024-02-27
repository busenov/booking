<?php

use booking\entities\Car\CarType;
use booking\entities\Car\Price;
use booking\entities\Slot\Slot;
use booking\forms\manage\Car\CarTypeForm;
use booking\forms\manage\Car\PriceForm;
use booking\forms\manage\Car\PricesForm;
use kartik\datecontrol\DateControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var PricesForm $model */
/** @var yii\widgets\ActiveForm $form */

$this->title = 'Редактировать цены машины: ' . $model->_carType->name;
$this->params['breadcrumbs'][] = ['label' => 'Машины', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->_carType->name, 'url' => ['view', 'id' => $model->_carType->id]];
$this->params['breadcrumbs'][] = 'Правка';
?>
<div class="car-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="prices-form">

        <?php $form = ActiveForm::begin(); ?>
        <?= Html::a('Добавить цену', Url::to(['add-empty-price','id'=>$model->_carType->id]),['class' => 'btn btn-success','data-method'=>'post']) ?>
        <?= $form->field($model, 'safe')->hiddenInput()->label(false) ?>
        <? foreach ($model->prices as $i=>$price) :?>
        <div class="row">
            <div class="col-md-2">
                <?= $form->field($price, '[' . $i . ']weekday')->dropDownList(Price::getWeekdayList(), ['prompt' => '']) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($price, '[' . $i . ']date_from')->widget(DateControl::class, [
                    'type'=>DateControl::FORMAT_TIME,
                    'displayFormat' => 'php:H:i',
                    'displayTimezone'=>'GMT',
                    'saveTimezone'=>'GMT',
                    'ajaxConversion'=>true,
                    'widgetOptions' => [
                        'options'=>[
                            'class'=>'price-time',
                        ],
                        'pluginOptions' => [
                            'autoclose' => true,
                            'minuteStep' => 5,
                        ]
                    ]
                ]);
                ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($price, '[' . $i . ']cost')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($price, '[' . $i . ']note')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-2">
                <br>
                <?if ($price->_price) :?>
                <?= Html::a('Удалить', Url::to(['delete-price','id'=>$model->_carType->id,'price_id'=>$price->_price->id]) ,[
                    'class' => 'btn btn-warning',
                    'data-confirm'=>'Вы уверены, что хотите удалить цену',
                    'data-method'=>'post'
                ]) ?>
                <?endif;?>
            </div>



        </div>
        <?endforeach;?>


        <br>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>