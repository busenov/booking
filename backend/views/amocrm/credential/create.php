<?php

use booking\forms\AmoCRM\CredentialForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var CredentialForm $model */

$this->title = 'Создать Доступ AmoCRM';
$this->params['breadcrumbs'][] = ['label' => 'Доступы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="companies-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
