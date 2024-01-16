<?php
namespace booking\repositories;

use booking\entities\Slot\Slot;
use Yii;
use yii\caching\TagDependency;

class SlotRepository
{

    public static function get_st($entityOrId): Slot
    {
        if (is_a($entityOrId,Slot::class)) {
            return $entityOrId;
        } else {
            return static::getBy(['id' => $entityOrId]);
        }
    }
    public function get($entityOrId): Slot
    {
        return static::get_st($entityOrId);
    }

    public function save(Slot $entity): void
    {
        if (!$entity->save()) {
            throw new \RuntimeException('Ошибка сохранения.');
        }
        TagDependency::invalidate(Yii::$app->cache, 'carType');
    }

    public function remove(Slot $entity): void
    {
        if (!$entity->delete()) {
            throw new \RuntimeException('Ошибка удаления.');
        }
        TagDependency::invalidate(Yii::$app->cache, 'carType');
    }
###Finds
    public static function find_st($entityOrId):?Slot
    {
        if (is_a($entityOrId,Slot::class)) {
            return $entityOrId;
        } else {
            return Slot::findOne($entityOrId);
        }
    }
    public function find($entityOrId):?Slot
    {
        return static::find_st($entityOrId);
    }


###

    private static function getBy(array $condition): Slot
    {
        if (!$entity = Slot::find()->andWhere($condition)->limit(1)->one()) {
            throw new NotFoundException('Слот не найден');
        }
        return $entity;
    }

    private function findAllBy(array $condition):array
    {
        return Slot::find()->andWhere($condition)->all();
    }

    private function findOneBy(array $condition):?Slot
    {
        return Slot::find()->andWhere($condition)->one();
    }
}