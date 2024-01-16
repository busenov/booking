<?php

namespace booking\entities\behaviors;
use booking\entities\ChangeLog;
use yii\base\Behavior;
use yii\base\Event;
use yii\base\UnknownPropertyException;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Поведение для логирования изменений
 * Для включения нужно в поведениях(behaviors) вставить следующий код:
 *  [
 *      'class' => LoggingBehavior::class,
 *      'ignoredAttributes' => ['created_at','updated_at','author_id','author_name','editor_id','editor_name'],
 *  ],
 *  Если 'ignoredAttributes' не указано, тогда по умолчанию блокируются аттрибуты в примере
 */
class LoggingBehavior extends Behavior
{
    private ?Event $beforeEvent=null;
    private ?int $dateTime=null;
    public function events():array
    {
        return [
            BaseActiveRecord::EVENT_AFTER_INSERT    => 'onAfterSave',
            BaseActiveRecord::EVENT_BEFORE_UPDATE   => 'onBeforeSave',
            BaseActiveRecord::EVENT_AFTER_UPDATE    => 'onAfterSave',
            BaseActiveRecord::EVENT_BEFORE_DELETE   => 'onBeforeDelete',
            BaseActiveRecord::EVENT_AFTER_DELETE    => 'onAfterDelete'
        ];
    }

    public $ignoredAttributes = [
        'created_at',
        'updated_at',
        'author_id',
        'author_name',
        'editor_id',
        'editor_name'
    ];
    public function init()
    {
        parent::init();

//        if ($this->hasProperty('ignoredFields')) {
//            $this->ignoredAttributes=$this->ignoredFields;
//        }
    }
    public function onBeforeSave(Event $event): void
    {
        $this->beforeEvent=$event;
        $this->dateTime=time();
    }

    /**
     * При сохранении.
     * Если Создание, тогда 1 запись
     * Иначе тогда запись по кол-во измененных аттрибутов
     * @param Event $event
     * @return void
     */
    public function onAfterSave(Event $event): void
    {
        $className=get_class($event->sender);
        if ($this->isInsert()) {
            $log=ChangeLog::create(
                $className,
                $event->sender->id,
                null,
                null,
                null,
                $this->dateTime,
                json_encode(ArrayHelper::toArray($event->sender)),
                ChangeLog::ACTION_INSERT
            );
            $log->save();
        } else {
            $changedAttributes=property_exists($event,'changedAttributes')?$event->changedAttributes:[];
            foreach ($changedAttributes as $changedAttribute=>$value) {
                if (in_array($changedAttribute,$this->ignoredAttributes)) continue;
                if ($value!=$event->sender->$changedAttribute) {
                    $log=ChangeLog::create(
                        $className,
                        $event->sender->id,
                        $changedAttribute,
                        (string)$value,
                        (string)$event->sender->$changedAttribute,
                        $this->dateTime,
                        json_encode(ArrayHelper::toArray($event->sender)),
                        ChangeLog::ACTION_UPDATE
                    );
                    $log->save();
                }
            }
        }

    }
    public function onBeforeDelete(Event $event): void
    {
        $this->beforeEvent=$event;
        $this->dateTime=time();
    }
    public function onAfterDelete(Event $event): void
    {
        $className=get_class($event->sender);
        $log=ChangeLog::create(
            $className,
            $event->sender->id,
            null,
            null,
            null,
            $this->dateTime,
            json_encode(ArrayHelper::toArray($event->sender)),
            ChangeLog::ACTION_DELETE
        );
        $log->save();
    }
###
    private function isInsert():bool
    {
        return empty($this->beforeEvent);
    }
}