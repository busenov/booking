<?php

use booking\entities\User\User;
use booking\helpers\UserHelper;
use backend\forms\UserSearch;
use backend\widgets\grid\RoleColumn;
use kartik\grid\GridViewInterface;
use kartik\widgets\DatePicker;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
//use yii\grid\GridView;
use kartik\grid\GridView;

/** @var yii\web\View $this */
/** @var UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить пользователя  ', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            [
                'attribute' => 'id',
                'width' => '5%',
            ],
            [
                'attribute' => 'shortName',
                'value' => function ($data) {
                    return Html::a(Html::encode($data->shortName), Url::to(['view', 'id' => $data->id]));
                },
                'format' => 'raw',
            ],

            'email:email',
            [
                'attribute' => 'role',
                'class' => RoleColumn::class,
                'filter' => $searchModel->rolesList(),
            ],
            [
                'attribute' => 'status',
                'width' => '10%',
                'value' => function (User $data) {
                    return UserHelper::statusLabel($data->status);
                },
                'format' => 'raw',
                'filterType' => GridViewInterface::FILTER_SELECT2,
                'filter' => User::getStatusList(),
                'filterWidgetOptions' => [
                    'hideSearch' => true,
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Статус', 'multiple' => false],
            ],
            [
                'attribute' => 'created_at',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'date_from',
                    'attribute2' => 'date_to',
                    'type' => DatePicker::TYPE_RANGE,
                    'separator' => '-',
                    'pluginOptions' => [
                        'todayHighlight' => true,
                        'autoclose'=>true,
                        'format' => 'yyyy-mm-dd',
                    ],
                ]),
                'format' => 'datetime',
            ],
//            'surname',
//            'patronymic',
//            'telephone',
//            'gender',
            //'created_at',
            //'updated_at',
            //'author_id',
            //'author_name',
            //'editor_id',
            //'editor_name',
            [
                'label' => 'Подключиться',
                'value' => function ($data) {
                    return Html::a('Войти', Url::to(['sign-in', 'id' => $data->id]),['class'=>'btn btn-danger']);
                },
                'format' => 'raw',
            ],
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, User $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
