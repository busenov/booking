<?php

use booking\access\Rbac;
use booking\entities\User\User;
use booking\helpers\UserHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var User $model */

$this->title = $model->shortName;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'email:email',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function (User $data) {
                    return User::statusName($data->status);
                },
            ],
            [
                'attribute' => 'role',
                'format' => 'raw',
                'value' => function (User $data) {
                    return '<a href="'.Url::to(['docs/rules']).'">'.implode(',',ArrayHelper::map($data->roles, 'name', 'description')).'</a>';
                },
            ],
            'name',
            'surname',
            'patronymic',
            'telephone',
            'gender',
            'created_at:datetime',
            'author_name',
            'updated_at:datetime',
            'editor_name',
        ],
    ]) ?>

</div>
