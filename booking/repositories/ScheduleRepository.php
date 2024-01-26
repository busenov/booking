<?php
namespace booking\repositories;

use booking\entities\Schedule\Schedule;
use Yii;
use yii\caching\TagDependency;
use function Symfony\Component\String\s;

class ScheduleRepository
{

    public static function get_st($entityOrId): Schedule
    {
        if (is_a($entityOrId,Schedule::class)) {
            return $entityOrId;
        } else {
            return static::getBy(['id' => $entityOrId]);
        }
    }
    public function get($entityOrId): Schedule
    {
        return static::get_st($entityOrId);
    }

    public function save(Schedule $entity): void
    {
        if (!$entity->save()) {
            throw new \RuntimeException('Ошибка сохранения.');
        }
        TagDependency::invalidate(Yii::$app->cache, 'schedule');
    }

    public function remove(Schedule $entity): void
    {
        if (!$entity->delete()) {
            throw new \RuntimeException('Ошибка удаления.');
        }
        TagDependency::invalidate(Yii::$app->cache, 'schedule');
    }
###Finds
    public static function find_st($entityOrId):?Schedule
    {
        if (is_a($entityOrId,Schedule::class)) {
            return $entityOrId;
        } else {
            return Schedule::findOne($entityOrId);
        }
    }
    public function find($entityOrId):?Schedule
    {
        return static::find_st($entityOrId);
    }
    public function findByWeekday(int $weekday):?Schedule
    {
        return $this->findOneBy(['weekday'=>$weekday]);
    }
    public function findByDate(?int $unixTime=null):?Schedule
    {
        $unixTime=$unixTime??time();
        return $this->findByWeekday(date('N',$unixTime));
    }

    /**
     * @return Schedule[]
     */
    public function findAll():array
    {
        return $this->findAllBy([]);
    }
    public static function findActive_st():?array
    {
        return Schedule::find()->where(['status'=>Schedule::STATUS_ACTIVE])->all();
    }
    public function findActive():?array
    {
        return static::findActive_st();
    }
###Sum

###

    private static function getBy(array $condition): Schedule
    {
        if (!$entity = Schedule::find()->andWhere($condition)->limit(1)->one()) {
            throw new NotFoundException('Расписание не найдено');
        }
        return $entity;
    }




    private function findAllBy(array $condition):array
    {
        return Schedule::find()->andWhere($condition)->all();
    }

    private function findOneBy(array $condition):?Schedule
    {
        return Schedule::find()->andWhere($condition)->one();
    }


}