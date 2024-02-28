<?php
namespace booking\repositories;

use booking\entities\AmoCRM\Credential;
use Yii;
use yii\caching\TagDependency;

class CredentialRepository
{

    public function get(int $id): Credential
    {
        return $this->getBy([
            'id' => $id,
        ]);
    }

    public function save(Credential $entity): void
    {
        if (!$entity->save()) {
            throw new \RuntimeException('Ошибка сохранения.');
        }
        TagDependency::invalidate(Yii::$app->cache, 'credential');
    }

    public function remove(Credential $entity): void
    {
        if (!$entity->delete()) {
            throw new \RuntimeException('Ошибка удаления.');
        }
        TagDependency::invalidate(Yii::$app->cache, 'credential');
    }
###Finds
    public static function find_st(int $id):?Credential
    {
            return Credential::find()->andWhere([
                'id' => $id,
            ])->one();
    }
    public function find(int $id):?Credential
    {
        return static::find_st($id);
    }

###
    private function getBy(array $condition): Credential
    {
        if (!$entity = Credential::find()->andWhere($condition)->limit(1)->one()) {
            throw new NotFoundException('Credential не найден');
        }
        return $entity;
    }


    private function findAllBy(array $condition):array
    {
        return Credential::find()->andWhere($condition)->all();
    }
    private function findOneBy(array $condition):?Credential
    {
        return Credential::find()->andWhere($condition)->one();
    }


}