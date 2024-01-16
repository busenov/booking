<?php
namespace booking\repositories;

use booking\access\Rbac;
use booking\entities\User\User;
use Yii;
use yii\caching\TagDependency;
use yii\rbac\DbManager;

class UserRepository
{


    public function get( $entityOrId): User
    {
        if (is_a($entityOrId,User::class)) {
            return $entityOrId;
        } else {
            return $this->getBy(['id' => $entityOrId]);
        }
    }

    public function getByEmailConfirmToken($token): User
    {
        return $this->getBy(['email_confirm_token' => $token]);
    }

    public function getByEmail($email): User
    {
        return $this->getBy(['email' => $email]);
    }

    public function getByPasswordResetToken($token): User
    {
        return $this->getBy(['password_reset_token' => $token]);
    }

    public function existsByPasswordResetToken(string $token): bool
    {
        return (bool) User::findByPasswordResetToken($token);
    }

    public function save(User $user): void
    {
        if (!$user->save()) {
            throw new \RuntimeException('Saving error.');
        }
        TagDependency::invalidate(Yii::$app->cache, 'users');
    }

    public function remove(User $user): void
    {
        if (!$user->delete()) {
            throw new \RuntimeException('Removing error.');
        }
        TagDependency::invalidate(Yii::$app->cache, 'users');
    }
###Finds
    public static function find_st($entityOrId):?User
    {
        if (is_a($entityOrId,User::class)) {
            return $entityOrId;
        } else {
            return User::findOne($entityOrId);
        }
    }
    public function find($entityOrId):?User
    {
        return static::find_st($entityOrId);
    }
    public function findByEmail($value): ?User
    {
        return $this->findOneBy(['email' => $value]);
    }
    public function findActiveByEmail($value): ?User
    {
        return $this->findOneBy(['status'=>User::STATUS_ACTIVE,'email' => $value]);
    }

    public function findActive():array
    {
        return $this->findAllBy(['status'=>User::STATUS_ACTIVE]);
    }
    public function findActiveWithoutUsers(array $userIds=[]):array
    {
        $query=User::find()->andWhere(['status'=>User::STATUS_ACTIVE]);
        if ($userIds) {
            $query->andWhere(['not in','id',$userIds]);
        }
        return $query->all();
    }
    public function findAdmins():array
    {
        return $this->findUserByRole(Rbac::ROLE_ADMIN);
    }
    /**
    Возращаем массив пользователей с ролью
     **/
    public function findUserByRole($role=Rbac::ROLE_USER):array
    {
        $users=array();
        /** @var DbManager $authManager */
        $authManager = Yii::$app->get('authManager');
        foreach ($authManager->getUserIdsByRole($role) as $id) {
            $users[]=$this->get($id);
        }
        return $users;
    }
###
    private function getBy(array $condition): User
    {
        if (!$user = User::find()->andWhere($condition)->limit(1)->one()) {
            throw new NotFoundException('Пользователь не найден');
        }
        return $user;
    }
    private function findAllBy(array $condition):array
    {
        return User::find()->andWhere($condition)->all();
    }
    private function findOneBy(array $condition):?User
    {
        return User::find()->andWhere($condition)->one();
    }


}