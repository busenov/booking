<?php

use backend\forms\CarTypeSearch;
use backend\forms\SlotSearch;
use booking\entities\Car\CarType;
use booking\entities\Slot\Slot;
use booking\forms\manage\Schedule\ScheduleForm;
use booking\forms\manage\Slot\GenerateForm;
use booking\forms\manage\Slot\SlotForm;
use booking\helpers\CarHelper;
use booking\helpers\DateHelper;
use booking\helpers\SlotHelper;
use booking\useCases\manage\CarTypeManageService;
use booking\useCases\manage\SlotManageService;
use kartik\editable\Editable;
use kartik\grid\GridViewInterface;
use kartik\widgets\SwitchInput;
use kartik\widgets\TouchSpin;
use yii\bootstrap5\ToggleButtonGroup;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var SlotSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var GenerateForm $scheduleForm */

$this->title = 'Заезды';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="slot-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'action' => ['slot/index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($searchModel, 'period')->widget(ToggleButtonGroup::class, [
        'type' => ToggleButtonGroup::TYPE_RADIO,
        'items' => $searchModel->getPeriodList(),
        'options' => [
            'class' =>'btn-group btn-group-toggle slot-period'
        ],
        'labelOptions' => [
            'class' => ['btn', 'btn-primary '],
            'wrapInput' => true
        ],


    ])->label(false) ?>


    <div class="form-group">
        <?= Html::resetButton('Сброс', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <div class="slot-day">
        <div class="row">
        </div>
    </div>

    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="btn-group ">
            <?php if (SlotManageService::guardCanCreate(true)) :?>
                <? if ($dataProvider->count==0) :?>
                    <?= Html::a('Сгенерировать заезды', '#', [
                        'class' => 'btn btn-success btn-generate'
                    ]) ?>
                <?endif;?>
                <div class="btn-group" role="group">
                    <button id="operations" type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        Операции
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="operations">
                        <li>
                            <?= Html::a(
                                'Активировать все заезды на '.date('d.m.y',$searchModel->period),
                                    Url::to(['change-status-all','unixTime'=>$searchModel->period,'status'=>Slot::STATUS_ACTIVE]),
                                    [
                                        'class' => 'dropdown-item'
                                    ])
                            ?>
                            <?= Html::a(
                                'Деактивировать все заезды на '.date('d.m.y',$searchModel->period),
                                    Url::to(['change-status-all','unixTime'=>$searchModel->period,'status'=>Slot::STATUS_NEW]),
                                    [
                                        'class' => 'dropdown-item'
                                    ])
                            ?>
                        </li>
                    </ul>
                </div>
                <?= Html::a('Активировать', '#', [
                    'class' => 'btn btn-primary btn-generate'
                ]) ?>
            <?php endif;?>
            </div>
        </div>
        <div class="col-md-6">
            <div class=" position-relative">
                <div class="btn-group  position-absolute end-0">
                    <a href="<?=Url::to(['clear-day','unixTime'=>$searchModel->period])?>" class="btn btn-danger">Очистить</a>
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
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'qty',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'width' => '9%',
                'headerOptions' => ['class' => 'kv-sticky-column'],
                'contentOptions' => ['class' => 'kv-sticky-column'],
                'readonly' => function(Slot $model, $key, $index, $widget) {
                    return ($model->readOnly());
                },
                'editableOptions' =>  function (Slot $model, $key, $index) {
                    return [
                        'header'=>'Кол-во',
                        'size'=>'md',
                        'inputType' => Editable::INPUT_HIDDEN,
                        'beforeInput' => function ($form, $widget) use ($model, $index) {
                            $slotForm=new SlotForm($model);
                            echo $form->field($slotForm, "qty")->widget(TouchSpin::class, [
                                'options'=>[
                                    'placeholder'=>'',
                                    'class'=>'',
                                    'id'=>$slotForm->formName().'-qty-'.$index
                                ],
                                'pluginOptions' => [
                                    'step' => 1,
                                    'max'=> Slot::getMaxQty()
                                ]
                            ])->label(false);
                        },
                    ];
                }
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'is_child',
                'value' => function (Slot $data) {
                    return Slot::getIsChildLabel($data->is_child);
                },
                'format' => 'raw',
                'width' => '10%',
                'readonly' => function(Slot $model, $key, $index, $widget) {
                    return ($model->readOnly());
                },
                'editableOptions' =>  function ($model, $key, $index) {
                    return [
                        'header'=>'Детский?',
                        'size'=>'md',
                        'inputType' => Editable::INPUT_HIDDEN,
                        'beforeInput' => function ($form, $widget) use ($model, $index) {
                            $model=new SlotForm($model);
                            echo $form->field($model, "is_child")->widget(SwitchInput::class, [
                                'options'=>[
                                    'placeholder'=>'',
                                    'class'=>'',
                                    'id'=> $model->formName().'-is_child-'.$index
                                ],
                                'pluginOptions' => [
                                    'onText'=>'Детский',
                                    'offText'=>'Взрослый'
                                ]
                            ])->label(false);
                        },
                    ];
                },
                'filterType' => GridViewInterface::FILTER_SELECT2,
                'filter' => [false=>'Взрослый',true=>'Детский'],
                'filterWidgetOptions' => [
                    'hideSearch' => true,
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => '', 'multiple' => false],

            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'note',
                'width' => '20%',
                'editableOptions' =>  function ($model, $key, $index) {
                    return [
                        'header'=>'Примечание',
                        'size'=>'md',
                        'inputType' => Editable::INPUT_HIDDEN,
                        'beforeInput' => function (ActiveForm $form, $widget) use ($model, $index) {
                            $model=new SlotForm($model);
                            echo $form->field($model, "note")->textInput([
                                'id'=> $model->formName().'-note-'.$index
                            ])->label(false);
                        },
                    ];
                },
            ],
            [
                'attribute' => 'countOrders',
                'value' => function (Slot $data) {
                    return Html::a(Html::encode(count($data->orders)), Url::to(['order/index', 'OrderSearch[slot_id]' => $data->id]));
                },
                'format' => 'raw',
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
<?= $this->render('generateModal', [
    'model' => $scheduleForm,
    'unixTime'=>$searchModel->period??time(),
]) ?>


<?
$js = <<<JS
    $("document").ready(function(){
        $(".slot-period").change(function (e){
            $(this).closest( "form" ).submit()
        })
        $(".btn-generate").click(function (e){
            var modal = new bootstrap.Modal(document.getElementById('generateSlots'), {
                keyboard: false
            })
            modal.show()    
        })
        

    });
JS;
$this->registerJs($js);
?>