<?php

/** @var yii\web\View $this */
/** @var array $calendar */
/** @var int $week */

use yii\helpers\Url;

$urlWeekNxt=Url::to(['get-calendar-ajax','week'=>$week+(1*60*60*24*7)]);
$urlWeekPre=Url::to(['get-calendar-ajax','week'=>$week+(-1*60*60*24*7)]);
?>

<button class="btn-change-week" data-action="<?=$urlWeekPre?>"><-- предыдущая неделя</button>
<div class="week__dates">
<? foreach ($calendar as $wDay=> $day) :?>
    <?php
    $colorLabel='';
    if (isset($day['qty'])) {
        if ($day['qty']==0) {
            $colorLabel='busy';
        } elseif ($day['qty']<=100) {
            $colorLabel='malo';
        } elseif($day['qty']<=200) {
            $colorLabel='malo';
        } elseif($day['qty']<=400) {
            $colorLabel='dostupno';
        }
    }

    ?>
    <div class="week__date
                <?=$day['isPast']?'past':''?>
                <?=$day['isCurrent']?'current':''?>
                <?=$day['isSelected']?'isActive':''?>
                <?=$day['isNoActive']?'noActive':''?>
                "
         data-wday="<?=$wDay?>"
         data-day="<?=$day['unixTime']?>"
    >
        <div class="week__date-count"><?=$day['day']?></div>
        <div class="week__date-label
                         <?=$colorLabel?>
                        "><?=$day['qty']??''?></div>
    </div>
<?endforeach;?>
</div>
<button class="btn-change-week" data-action="<?=$urlWeekNxt?>"> следующая неделя --></button>
