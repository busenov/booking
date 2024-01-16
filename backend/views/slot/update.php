<?php

use booking\forms\manage\Car\CarTypeForm;
use booking\forms\manage\Slot\SlotForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var SlotForm $model */

$this->title = 'Редактировать Машину(карт): ' . $model->_slot->getName();
$this->params['breadcrumbs'][] = ['label' => 'Машины', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->_slot->getName(), 'url' => ['view', 'id' => $model->_slot->id]];
$this->params['breadcrumbs'][] = 'Правка';
?>
<div class="slot-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
