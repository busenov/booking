<?php

use booking\entities\Car\CarType;
use booking\helpers\CarHelper;
use booking\useCases\manage\CarTypeManageService;
use booking\useCases\manage\ScheduleManageService;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var \booking\entities\Schedule\Schedule $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Расписания', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="schedule-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (ScheduleManageService::guardCanEdit($model,true)) :?>
        <?= Html::a('Правка', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php endif;?>
        <?php if (ScheduleManageService::guardCanRemove($model,true)) :?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить?',
                'method' => 'post',
            ],
        ]) ?>
        <?php endif;?>
        <?php if (ScheduleManageService::guardCanRemoveHard($model,true)) :?>
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
            'weekday',
            'begin',
            'end',
            'duration',
            'interval',
            'sort',
            'note',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function (CarType $data) {
                    return CarHelper::statusLabel($data->status) ;
                },
            ],
            'created_at:datetime',
            'author_name',
            'updated_at:datetime',
            'editor_name',
        ],
    ]) ?>


</div>
