<?php

use backend\forms\CarTypeSearch;
use backend\forms\SlotSearch;
use booking\entities\Car\CarType;
use booking\entities\Slot\Slot;
use booking\helpers\CarHelper;
use booking\helpers\DateHelper;
use booking\helpers\SlotHelper;
use booking\useCases\manage\CarTypeManageService;
use booking\useCases\manage\SlotManageService;
use kartik\grid\GridViewInterface;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var SlotSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Слоты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="slot-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-6">
            <?php if (SlotManageService::guardCanCreate(true)) :?>
                <?= Html::a('Создать слот', ['create'], ['class' => 'btn btn-success']) ?>
            <?php endif;?>
        </div>
        <div class="col-md-6">
            <div class=" position-relative">
                <div class="btn-group  position-absolute end-0">
<!--                    <a href="--><?php //=Url::to(['create','weekday'=>Schedule::WEEKDAY_WORK])?><!--" class="btn btn btn-success">Создать расписание на будни</a>-->
<!--                    <a href="--><?php //=Url::to(['create','weekday'=>Schedule::WEEKDAY_WEEKEND])?><!--" class="btn btn-primary">Создать расписание на выходные</a>-->
                    <a href="<?=Url::to(['clear'])?>" class="btn btn-danger">Очистить</a>
                </div>
            </div>
        </div>
    </div>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'width' => '5%',
            ],
            [
                'attribute' => 'date',
                'value' => function (Slot $data) {
                    return Html::a(Html::encode($data->getName()), Url::to(['view', 'id' => $data->id]));
                },
                'format' => 'raw',
                'width' => '20%',
            ],
            [
                'attribute' => 'begin',
                'value' => function (Slot $data) {
                    return DateHelper::timeIntToStr($data->begin,false);
                },
                'format' => 'raw',

            ],
            [
                'attribute' => 'end',
                'value' => function (Slot $data) {
                    return DateHelper::timeIntToStr($data->end,false);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'qty',
            ],
            [
                'attribute' => 'note',
                'width' => '20%',
            ],
            [
                'attribute' => 'status',
                'width' => '10%',
                'value' => function (Slot $data) {
                    return SlotHelper::statusLabel($data->status);
                },
                'format' => 'raw',
                'filterType' => GridViewInterface::FILTER_SELECT2,
                'filter' => Slot::getStatusList(),
                'filterWidgetOptions' => [
                    'hideSearch' => true,
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Статус', 'multiple' => false],
            ],
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, Slot $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'buttons'=>[
                    'delete-hard'=>function ($url, Slot $data){
                        return Html::a(
                            '<svg aria-hidden="true" class="text-danger" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:.875em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M32 464a48 48 0 0048 48h288a48 48 0 0048-48V128H32zm272-256a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zM432 32H312l-9-19a24 24 0 00-22-13H167a24 24 0 00-22 13l-9 19H16A16 16 0 000 48v32a16 16 0 0016 16h416a16 16 0 0016-16V48a16 16 0 00-16-16z"/></svg>',
                            Url::to(['delete-hard', 'id' => $data->id]),
                            [
                                'title' => 'Жестко удалить',
                                'data-confirm'=>'Вы уверены, что хотите жестко удалить этот элемент? Восстановление не возможно.',
                                'data-method'=>'post'
                            ]);

                    }
                ],
                'template' => '{view}{update}{delete}{delete-hard}',
                'visibleButtons'=> [
                    'view'=> function(Slot $entity) {
                        return SlotManageService::guardCanView($entity, true);
                    },
                    'update'=> function(Slot $entity) {
                        return SlotManageService::guardCanEdit($entity, true);
                    },
                    'delete'=> function(Slot $entity) {
                        return SlotManageService::guardCanRemove($entity, true);
                    },
                    'delete-hard'=> function(Slot $entity) {
                        return SlotManageService::guardCanRemoveHard($entity, true);
                    }

                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
