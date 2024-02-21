<?php

use booking\forms\manage\Car\CarTypeForm;
use booking\forms\manage\License\LicenseForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var LicenseForm $model */

$this->title = 'Добавить водительские права';
$this->params['breadcrumbs'][] = ['label' => 'Права', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="license-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
