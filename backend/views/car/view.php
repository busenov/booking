<?php

use booking\entities\Car\CarType;
use booking\entities\Slot\Slot;
use booking\helpers\CarHelper;
use booking\useCases\manage\CarTypeManageService;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var CarType $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Машины', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="car-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (CarTypeManageService::guardCanEdit($model,true)) :?>
        <?= Html::a('Правка', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php endif;?>
        <?php if (CarTypeManageService::guardCanRemove($model,true)) :?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить?',
                'method' => 'post',
            ],
        ]) ?>
        <?php endif;?>
        <?php if (CarTypeManageService::guardCanRemoveHard($model,true)) :?>
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
            'name',
            'description',
            'qty',
            'pwr',
            'note',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function (CarType $data) {
                    return CarHelper::statusLabel($data->status) ;
                },
            ],
            [
                'attribute' => 'type',
                'format' => 'raw',
                'value' => function (CarType $data) {
                    return Slot::getTypeName($data->type);
                },
            ],
            'created_at:datetime',
            'author_name',
            'updated_at:datetime',
            'editor_name',
        ],
    ]) ?>


</div>
