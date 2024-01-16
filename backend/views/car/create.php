<?php

use booking\forms\manage\Car\CarTypeForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var CarTypeForm $model */

$this->title = 'Создать машину(карт)';
$this->params['breadcrumbs'][] = ['label' => 'Машины', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="car-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
