<?php

use backend\forms\CarTypeSearch;
use booking\entities\Car\CarType;
use booking\entities\Schedule\Schedule;
use booking\helpers\CarHelper;
use booking\helpers\DateHelper;
use booking\helpers\ScheduleHelper;
use booking\useCases\manage\CarTypeManageService;
use booking\useCases\manage\ScheduleManageService;
use kartik\grid\GridViewInterface;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use kartik\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var \backend\forms\ScheduleSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Расписания';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="schedule-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-6">
            <?php if (ScheduleManageService::guardCanCreate(true)) :?>
                <?= Html::a('Создать Расписание', ['create'], ['class' => 'btn btn-success']) ?>
            <?php endif;?>
        </div>
        <div class="col-md-6">
            <div class=" position-relative">
                <div class="btn-group  position-absolute end-0">
                    <a href="<?=Url::to(['schedule/create','weekday'=>Schedule::WEEKDAY_WORK])?>" class="btn btn btn-success">Создать расписание на будни</a>
                    <a href="<?=Url::to(['schedule/create','weekday'=>Schedule::WEEKDAY_WEEKEND])?>" class="btn btn-primary">Создать расписание на выходные</a>
                    <a href="<?=Url::to(['schedule/clear'])?>" class="btn btn-danger">Очистить</a>
                </div>
            </div>
        </div>
    </div>
    <br>
    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'weekday',
                'value' => function (Schedule $data) {
                    return Schedule::weekdayName($data->weekday);
                },
                'format' => 'raw',
                'width' => '20%',
            ],
            [
                'attribute' => 'begin',
                'value' => function (Schedule $data) {
                    return DateHelper::timeIntToStr($data->begin,false);
                },
                'format' => 'raw',
                'width' => '20%',
            ],
            [
                'attribute' => 'end',
                'value' => function (Schedule $data) {
                    return DateHelper::timeIntToStr($data->end,false);
                },
                'format' => 'raw',
                'width' => '20%',
            ],
            [
                'attribute' => 'duration',
                'value' => function (Schedule $data) {
                    return round($data->duration/60);
                },
                'format' => 'raw',
                'width' => '20%',
            ],
            [
                'attribute' => 'interval',
                'value' => function (Schedule $data) {
                    return round($data->interval/60);
                },
                'format' => 'raw',
                'width' => '20%',
            ],
            [
                'attribute' => 'note',
                'width' => '20%',
            ],

            [
                'attribute' => 'status',
                'width' => '10%',
                'value' => function (Schedule $data) {
                    return ScheduleHelper::statusLabel($data->status);
                },
                'format' => 'raw',
                'filterType' => GridViewInterface::FILTER_SELECT2,
                'filter' => Schedule::getStatusList(),
                'filterWidgetOptions' => [
                    'hideSearch' => true,
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Статус', 'multiple' => false],
            ],
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, Schedule $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'buttons'=>[
                    'delete-hard'=>function ($url, Schedule $data){
                        return Html::a(
                            '<svg aria-hidden="true" class="text-danger" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:.875em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M32 464a48 48 0 0048 48h288a48 48 0 0048-48V128H32zm272-256a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zM432 32H312l-9-19a24 24 0 00-22-13H167a24 24 0 00-22 13l-9 19H16A16 16 0 000 48v32a16 16 0 0016 16h416a16 16 0 0016-16V48a16 16 0 00-16-16z"/></svg>',
                            Url::to(['delete-hard', 'id' => $data->id]),
                            [
                                'title' => 'Жестко удалить',
                                'data-confirm'=>'Вы уверены, что хотите жестко удалить этот элемент? Восстановление не возможно.'
                            ]);

                    }
                ],
                'template' => '{delete-hard}',
                'visibleButtons'=> [
                    'view'=> function(Schedule $entity) {
                        return ScheduleManageService::guardCanView($entity, true);
                    },
                    'update'=> function(Schedule $entity) {
                        return ScheduleManageService::guardCanEdit($entity, true);
                    },
                    'delete'=> function(Schedule $entity) {
                        return ScheduleManageService::guardCanRemove($entity, true);
                    },
                    'delete-hard'=> function(Schedule $entity) {
                        return ScheduleManageService::guardCanRemoveHard($entity, true);
                    }

                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
