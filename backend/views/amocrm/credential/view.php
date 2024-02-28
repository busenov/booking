<?php

use booking\entities\AmoCRM\Credential;
use booking\useCases\manage\AmoCRM\CredentialManageService;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var Credential $model */

$this->title = $model->domain;
$this->params['breadcrumbs'][] = ['label' => 'Доступы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="clients-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (CredentialManageService::guardCanEdit($model,true)) :?>
        <?= Html::a('Правка', ['update', 'widget_id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php endif;?>
        <?php if (CredentialManageService::guardCanRemove($model,true)) :?>
        <?= Html::a('Удалить', ['delete', 'widget_id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить?',
                'method' => 'post',
            ],
        ]) ?>
        <?php endif;?>
    </p>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'domain',
            'token',
            'refresh_token',
            'expires:datetime',
            'widget_client_id',
            'client_secret',
            'redirect_uri',
        ],
    ]) ?>


</div>
