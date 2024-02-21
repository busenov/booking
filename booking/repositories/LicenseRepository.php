<?php
namespace booking\repositories;

use booking\entities\License\License;
use Yii;
use yii\caching\TagDependency;

class LicenseRepository
{

    public static function get_st($entityOrId): License
    {
        if (is_a($entityOrId,License::class)) {
            return $entityOrId;
        } else {
            return static::getBy(['id' => $entityOrId]);
        }
    }
    public function get($entityOrId): License
    {
        return static::get_st($entityOrId);
    }

    public function save(License $entity): void
    {
        if (!$entity->save()) {
            throw new \RuntimeException('Ошибка сохранения.');
        }
        TagDependency::invalidate(Yii::$app->cache, 'license');
    }

    public function remove(License $entity): void
    {
        if (!$entity->delete()) {
            throw new \RuntimeException('Ошибка удаления.');
        }
        TagDependency::invalidate(Yii::$app->cache, 'license');
    }
###Finds
    public static function find_st($entityOrId):?License
    {
        if (is_a($entityOrId,License::class)) {
            return $entityOrId;
        } else {
            return License::findOne($entityOrId);
        }
    }
    public function find($entityOrId):?License
    {
        return static::find_st($entityOrId);
    }
    public static function findActive_st():?array
    {
        return License::find()->where(['status'=>License::STATUS_ACTIVE])->all();
    }
    public function findByNumber(int $number):?License
    {
        return License::findOne(['number'=>$number]);
    }
###

    private static function getBy(array $condition): License
    {
        if (!$entity = License::find()->andWhere($condition)->limit(1)->one()) {
            throw new NotFoundException('Права не найдены');
        }
        return $entity;
    }




    private function findAllBy(array $condition):array
    {
        return License::find()->andWhere($condition)->all();
    }

    private function findOneBy(array $condition):?License
    {
        return License::find()->andWhere($condition)->one();
    }
}