<?php

use backend\forms\CarTypeSearch;
use booking\entities\Car\CarType;
use booking\entities\License\License;
use booking\helpers\CarHelper;
use booking\helpers\LicenseHelper;
use booking\useCases\manage\CarTypeManageService;
use booking\useCases\manage\LicenseManageService;
use kartik\grid\GridViewInterface;
use kartik\widgets\DatePicker;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var \backend\forms\LicenseSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Водительские права';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="slot-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (LicenseManageService::guardCanCreate(true)) :?>
    <p>
        <?= Html::a('Добавить права', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php endif;?>

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
                'attribute' => 'number',
                'value' => function (License $data) {
                    return Html::a(Html::encode($data->getName()), Url::to(['view', 'id' => $data->id]));
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'user_id',
                'value' => function (License $data) {
                    return Html::a(Html::encode($data->user->getShortName()), Url::to(['user/view', 'id' => $data->user_id]));
                },
                'format' => 'raw',
                'width' => '20%',
            ],
//            [
//                'attribute' => 'date',
////                'filter' => DatePicker::widget([
////                    'model' => $searchModel,
////                    'attribute' => 'date_from',
////                    'attribute2' => 'date_to',
////                    'type' => DatePicker::TYPE_RANGE,
////                    'separator' => '-',
////                    'pluginOptions' => [
////                        'todayHighlight' => true,
////                        'autoclose'=>true,
////                        'format' => 'yyyy-mm-dd',
////                    ],
////                ]),
//                'format' => 'datetime',
//            ],
            [
                'attribute' => 'note',
                'width' => '20%',
            ],
            [
                'attribute' => 'status',
                'width' => '10%',
                'value' => function (License $data) {
                    return LicenseHelper::statusLabel($data->status);
                },
                'format' => 'raw',
                'filterType' => GridViewInterface::FILTER_SELECT2,
                'filter' => License::getStatusList(),
                'filterWidgetOptions' => [
                    'hideSearch' => true,
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Статус', 'multiple' => false],
            ],
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, License $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'buttons'=>[
                    'delete-hard'=>function ($url, License $data){
                        return Html::a(
                            '<svg aria-hidden="true" class="text-danger" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:.875em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M32 464a48 48 0 0048 48h288a48 48 0 0048-48V128H32zm272-256a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zM432 32H312l-9-19a24 24 0 00-22-13H167a24 24 0 00-22 13l-9 19H16A16 16 0 000 48v32a16 16 0 0016 16h416a16 16 0 0016-16V48a16 16 0 00-16-16z"/></svg>',
                            Url::to(['delete-hard', 'id' => $data->id]),
                            [
                                'title' => 'Жестко удалить',
                                'data-confirm'=>'Вы уверены, что хотите жестко удалить этот элемент? Восстановление не возможно.'
                            ]);

                    }
                ],
                'template' => '{view}{update}{delete}{delete-hard}',
                'visibleButtons'=> [
                    'view'=> function(License $entity) {
                        return LicenseManageService::guardCanView($entity, true);
                    },
                    'update'=> function(License $entity) {
                        return LicenseManageService::guardCanEdit($entity, true);
                    },
                    'delete'=> function(License $entity) {
                        return LicenseManageService::guardCanRemove($entity, true);
                    },
                    'delete-hard'=> function(License $entity) {
                        return LicenseManageService::guardCanRemoveHard($entity, true);
                    }

                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
