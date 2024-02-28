<?php

use backend\forms\AmoCRM\CredentialSearch;
use booking\entities\AmoCRM\Credential;
use booking\useCases\manage\AmoCRM\CredentialManageService;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use kartik\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var CredentialSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Доступы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="widgets-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (CredentialManageService::guardCanCreate(true)) :?>
    <p>
        <?= Html::a('Создать Доступ', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php endif;?>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'domain',
                'value' => function (Credential $data) {
                    return Html::a(Html::encode($data->domain), Url::to(['view', 'id' => $data->id]));
                },
                'format' => 'raw',
            ],
            'expires:datetime',
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, Credential $data, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $data->id]);
                },
                'template' => '{view}{update}{delete}',
                'visibleButtons'=> [
                    'view'=> function(Credential $entity) {
                        return CredentialManageService::guardCanView($entity, true);
                    },
                    'update'=> function(Credential $entity) {
                        return CredentialManageService::guardCanEdit($entity, true);
                    },
                    'delete'=> function(Credential $entity) {
                        return CredentialManageService::guardCanRemove($entity, true);
                    },
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
