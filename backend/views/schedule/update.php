<?php

use booking\forms\manage\Schedule\ScheduleForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var ScheduleForm $model */

$this->title = 'Редактировать Расписание: ' . $model->_schedule->id;
$this->params['breadcrumbs'][] = ['label' => 'Расписания', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->_schedule->id, 'url' => ['view', 'id' => $model->_schedule->id]];
$this->params['breadcrumbs'][] = 'Правка';
?>
<div class="schedule-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
