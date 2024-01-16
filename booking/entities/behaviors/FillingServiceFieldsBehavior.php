<?php

namespace booking\entities\behaviors;

use booking\entities\User\User;
use booking\helpers\AppHelper;
use Yii;
use yii\base\Behavior;
use yii\base\Event;
use yii\db\ActiveRecord;

class FillingServiceFieldsBehavior extends Behavior
{
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'onBeforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'onBeforeSave',
        ];
    }

    public function onBeforeSave(Event $event): void
    {
        $model = $event->sender;

        if (AppHelper::isConsole()) return;

        if (($model->canGetProperty('author_id')and $model->getAttribute('author_id')==null)) $model->setAttribute('author_id',\Yii::$app->user->id);
        if (($model->canGetProperty('author_name')and $model->getAttribute('author_name')==null)) {
            if ($user=User::findOne(\Yii::$app->user->id)) {
                $model->setAttribute('author_name',$user->getShortName());
            }
        }

        if (($model->canGetProperty('editor_id')and $model->getAttribute('editor_id')==null)) $model->setAttribute('editor_id',\Yii::$app->user->id);
        if (($model->canGetProperty('editor_name')and $model->getAttribute('editor_name')==null)) {
            if ($user=User::findOne(\Yii::$app->user->id)) {
                $model->setAttribute('editor_name',$user->getShortName());
            }
        }

        if (Yii::$app->id=='app-console') return;

    }

}