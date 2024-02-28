<?php

use booking\entities\Order\Order;
use booking\entities\Slot\Slot;
use booking\helpers\OrderHelper;
use booking\helpers\SlotHelper;
use booking\useCases\manage\OrderManageService;
use booking\useCases\manage\SlotManageService;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var Order $model */

$this->title = $model->getName();
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="slot-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (OrderManageService::guardCanEdit($model,true)) :?>
        <?= Html::a('Правка', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php endif;?>
        <?php if (OrderManageService::guardCanRemove($model,true)) :?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить?',
                'method' => 'post',
            ],
        ]) ?>
        <?php endif;?>
        <?php if (OrderManageService::guardCanRemoveHard($model,true)) :?>
        <?= Html::a('Удалить Жестко', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите жестко удалить?',
                'method' => 'post',
            ],
        ]) ?>
        <?php endif;?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
//            'date:datetime',
//            'begin',
//            'end',
//            'qty',
            'note',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function (Order $data) {
                    return OrderHelper::statusLabel($data->status) ;
                },
            ],
            [
                'label'=>'Оплатить до:',
                'format' => 'raw',
                'value' => function (Order $data) {
                    return $data->date_begin_reserve?date('d.m.y H:i',$data->date_begin_reserve):'';
                },
            ],
//            'additional_info_json',
            'created_at:datetime',
            'author_name',
            'updated_at:datetime',
            'editor_name',
        ],
    ]) ?>

    <?if ($model->items):?>
    <table class="table">
        <thead>
        <tr>
            <th scope="col">Время заезда</th>
            <th scope="col">Машина</th>
            <th scope="col">Кол-во</th>
            <th scope="col">Цена</th>
            <th scope="col">Стоимость</th>
        </tr>
        </thead>
        <tbody>
        <?foreach ($model->items as $item) :?>
        <tr>
            <td><?=$item->slot->getName()?></td>
            <td><?=$item->carType->name?></td>
            <td><?=$item->qty?></td>
            <td><?=$item->price?></td>
            <td><?=$item->total?></td>
        </tr>
        <?endforeach;?>
        <tr class="table-success">
            <td><b>ИТОГО:</b></td>
            <td></td>
            <td></td>
            <td></td>
            <td><b><?=$model->total?></b></td>
        </tr>
        <tbody>
    </table>
    <?else:?>
        Нет заездов
    <?endif;?>
</div>
