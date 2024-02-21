<?php

use booking\forms\manage\Car\CarTypeForm;
use booking\forms\manage\User\LicenseForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var LicenseForm $model */

$this->title = 'Права: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Права', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->_license->id]];
$this->params['breadcrumbs'][] = 'Правка';
?>
<div class="car-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
