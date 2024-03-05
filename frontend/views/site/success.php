<?php


/** @var yii\web\View $this */
/** @var string $orderId */

use booking\entities\Order\Order;
use booking\forms\manage\Order\RacersForm;
use frontend\assets\BookingAsset;
use kartik\datecontrol\DateControl;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Url;
use yii\web\YiiAsset;

YiiAsset::register($this);
$this->title = Yii::$app->name;

$urlPre=Url::to(['index','step'=>1]);
$urlNxt='';
?>
<script type='text/javascript'>
</script>
<section class="body">
    <div class="header next-step">
        <div class="header__wrapper">
            <a class="head-link prev" href="<?= $urlPre ?>">< Заказать новый заезд</a>
            <div class="month noIcon">Данные успешно сохранились!</div>
        </div>
    </div>
    <div class="order-result__block">
        <div class="order-result__text">Номер брони: <div class="order-result__number"><?=$orderId?></div>. Для подтверждения с вами свяжутся наши менеджеры.</div>
    </div>
    </div>


</section>