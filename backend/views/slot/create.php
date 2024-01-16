<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var \booking\forms\manage\Slot\SlotForm $model */

$this->title = 'Создать слот';
$this->params['breadcrumbs'][] = ['label' => 'Слоты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="slot-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
