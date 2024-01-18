<?php

use booking\entities\Schedule\Schedule;
use booking\forms\manage\Car\CarTypeForm;
use booking\forms\manage\Schedule\ScheduleForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var ScheduleForm $model */

$this->title = 'Создать расписание' . (($model->weekday<0)?' на '. Schedule::weekdayName($model->weekday):'');
$this->params['breadcrumbs'][] = ['label' => 'Расписания', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="schedule-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
