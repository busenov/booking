<?php

use booking\forms\manage\Car\CarTypeForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var CarTypeForm $model */

$this->title = 'Редактировать Машину(карт): ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Машины', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->_carType->id]];
$this->params['breadcrumbs'][] = 'Правка';
?>
<div class="client-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
