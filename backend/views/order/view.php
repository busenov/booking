<?php

use booking\entities\Slot\Slot;
use booking\helpers\SlotHelper;
use booking\useCases\manage\SlotManageService;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var Slot $model */

$this->title = $model->getName();
$this->params['breadcrumbs'][] = ['label' => 'Слоты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="slot-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (SlotManageService::guardCanEdit($model,true)) :?>
        <?= Html::a('Правка', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php endif;?>
        <?php if (SlotManageService::guardCanRemove($model,true)) :?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить?',
                'method' => 'post',
            ],
        ]) ?>
        <?php endif;?>
        <?php if (SlotManageService::guardCanRemoveHard($model,true)) :?>
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
            'date:datetime',
            'begin',
            'end',
            'qty',
            'note',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function (Slot $data) {
                    return SlotHelper::statusLabel($data->status) ;
                },
            ],
            'created_at:datetime',
            'author_name',
            'updated_at:datetime',
            'editor_name',
        ],
    ]) ?>


</div>
