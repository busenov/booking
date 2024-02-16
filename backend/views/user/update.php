<?php

use booking\forms\manage\User\UserEditForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var UserEditForm $model */

$this->title = 'Правка пользователя: ' . $model->_user->shortName;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->_user->shortName, 'url' => ['view', 'id' => $model->_user->id]];
$this->params['breadcrumbs'][] = 'Правка';
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
