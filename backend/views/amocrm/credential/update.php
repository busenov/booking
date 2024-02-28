<?php

use booking\forms\AmoCRM\CredentialForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var CredentialForm $model */

$this->title = 'Редактировать Доступ: ' . $model->domain;
$this->params['breadcrumbs'][] = ['label' => 'Доступы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->domain, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Правка';
?>
<div class="widget-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
