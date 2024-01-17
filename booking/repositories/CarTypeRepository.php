<?php
namespace booking\repositories;

use booking\repositories\NotFoundException;
use booking\entities\Car\CarType;
use Yii;
use yii\caching\TagDependency;

class CarTypeRepository
{

    public static function get_st($entityOrId): CarType
    {
        if (is_a($entityOrId,CarType::class)) {
            return $entityOrId;
        } else {
            return static::getBy(['id' => $entityOrId]);
        }
    }
    public function get($entityOrId): CarType
    {
        return static::get_st($entityOrId);
    }

    public function save(CarType $entity): void
    {
        if (!$entity->save()) {
            throw new \RuntimeException('Ошибка сохранения.');
        }
        TagDependency::invalidate(Yii::$app->cache, 'carType');
    }

    public function remove(CarType $entity): void
    {
        if (!$entity->delete()) {
            throw new \RuntimeException('Ошибка удаления.');
        }
        TagDependency::invalidate(Yii::$app->cache, 'carType');
    }
###Finds
    public static function find_st($entityOrId):?CarType
    {
        if (is_a($entityOrId,CarType::class)) {
            return $entityOrId;
        } else {
            return CarType::findOne($entityOrId);
        }
    }
    public function find($entityOrId):?CarType
    {
        return static::find_st($entityOrId);
    }
###Sum
    public function sumActiveCar():int
    {
        return intval(CarType::find()->where(['status'=>CarType::STATUS_ACTIVE])->sum('qty'));
    }

###

    private static function getBy(array $condition): CarType
    {
        if (!$entity = CarType::find()->andWhere($condition)->limit(1)->one()) {
            throw new NotFoundException('Машина не найдена');
        }
        return $entity;
    }



    private function findAllBy(array $condition):array
    {
        return CarType::find()->andWhere($condition)->all();
    }

    private function findOneBy(array $condition):?CarType
    {
        return CarType::find()->andWhere($condition)->one();
    }
}