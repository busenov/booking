<?php

namespace booking\entities\User;

use booking\entities\behaviors\FillingServiceFieldsBehavior;
use booking\entities\behaviors\LoggingBehavior;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $email_confirm_token
 * @property string $auth_key
 * @property integer $status
 * @property string $password write-only password
 * @property integer $name
 * @property integer $surname
 * @property integer $patronymic
 * @property string $telephone
 * @property integer $gender
 * @property string $shortName
 * @property array $roles
 *
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $author_id
 * @property string $author_name
 * @property integer $editor_id
 * @property string $editor_name
 *
 *
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_WAIT = 9;
    const STATUS_ACTIVE = 10;
    const GENDER_MAN=1;
    const GENDER_WOMAN=2;
    const PWD_MIN_LENGTH=6;

    public static function create(
        string  $name,
        string  $email,
        ?string $password,
        ?string $surname,
        ?string $patronymic,
        ?string $telephone,
        ?int    $gender=null,
        ?int    $status=null,
    ): self
    {
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->setPassword(!empty($password) ? $password : Yii::$app->security->generateRandomString());
        $user->surname =$surname;
        $user->patronymic =$patronymic;
        $user->telephone =$telephone;
        $user->gender =$gender;
        $user->status = $status??self::STATUS_ACTIVE;
        $user->auth_key = Yii::$app->security->generateRandomString();
        return $user;
    }



    public function edit(
        string  $name,
        string  $email,
        ?string $surname=null,
        ?string $patronymic=null,
        ?string $telephone=null,
        ?int    $gender=null,
        ?int    $status=null,
    ): void
    {
        $this->name = $name;
        $this->email = $email;
        $this->surname =$surname;
        $this->patronymic =$patronymic;
        $this->telephone =$telephone;
        $this->gender =$gender;
        $this->status =$status;
    }


    public static function requestSignup(string $name,string $surname,string $email, string $password): self
    {
        $user = new static();
        $user->name=$name;
        $user->surname=$surname;
        $user->email=$email;
        $user->setPassword($password);
        $user->status=self::STATUS_WAIT;
        $user->email_confirm_token = Yii::$app->security->generateRandomString();
        $user->generateAuthKey();
        return $user;
    }

    public function confirmSignup(): void
    {
        if (!$this->isWait()) {
            throw new \DomainException('Пользователь уже активен');
        }
        $this->status = self::STATUS_ACTIVE;
        $this->email_confirm_token = null;
    }

    /**
     * Запрос на сброс пароля. Прежде чем сбрасываем пароль, проверяем не сброшен ли он ранее
     *
     * @throws \yii\base\Exception
     */
    public function requestPasswordReset(): void
    {
//        if (self::isPasswordResetTokenValid($this->password_reset_token)) {
//            throw new \DomainException('Сброс пароля уже запрошен.');
//        }
        $this->password_reset_token = $this->generatePasswordResetToken();
    }

    public function resetPassword($password): void
    {
        if (empty($this->password_reset_token))
            throw new \DomainException('Запрос на смену пароля не был отправлен.');
        if (!self::isPasswordResetTokenValid($this->password_reset_token))
            throw new \DomainException('Запрос на смену пароля истек.');
        $this->setPassword($password);
        $this->password_reset_token = null;
    }

    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isIdEqualTo($id): bool
    {
        return $this->id == $id;
    }

    /**
     *    Формируем Короткое имя пользователя
     **/
    public function getShortName(): string
    {
        return $this->name . ' ' . $this->surname;
    }
    /**
     *    Формируем полное имя пользователя
     **/
    public function getFullName(): string
    {
        return $this->name . ' ' .(!empty($this->patronymic))?$this->patronymic:''. ' ' . $this->surname;
    }

    public static function getGenderList():array
    {
        return [
            self::GENDER_MAN=>'Мужчина',
            self::GENDER_WOMAN=>'Женщина'
        ];
    }
    public static function getGenderName(int $gender):string
    {
        return self::getGenderList()[$gender];
    }

    public static function getStatusList(): array
    {
        return [
            User::STATUS_ACTIVE => 'Активен',
            User::STATUS_WAIT => 'Ожидание',
            User::STATUS_DELETED => 'Удален',

        ];
    }

    public static function statusName($status): string
    {
        return ArrayHelper::getValue(self::getStatusList(), $status);
    }

    public static function getRolesList(): array
    {
        return ArrayHelper::map(\Yii::$app->authManager->getRoles(), 'name', 'description');
    }

    public function getRoles()
    {
        return Yii::$app->authManager->getRolesByUser($this->id);
    }

    public static function getPasswordMinimum():int
    {
        return self::PWD_MIN_LENGTH;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels():array
    {
        return static::getAttributeLabels();
    }
    public static function getAttributeLabels():array
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'status' => 'Статус',
            'name' => 'Имя',
            'shortName' => 'ФИО',
            'role' => 'Роль',
            'surname' => 'Фамилия',
            'patronymic' => 'Отчество',
            'telephone' => 'Телефон',
            'gender' => 'Пол',

            'created_at' => 'Создано',
            'updated_at' => 'Отредактировано',
            'author_id' => 'Автор',
            'author_name' => 'Автор',
            'editor_id' => 'Редактор',
            'editor_name' => 'Редактор',
        ];
    }

###
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%users}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            FillingServiceFieldsBehavior::class,
            [
                'class' => LoggingBehavior::class,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function getAvatarUrl($size=null)
    {
//        if ($this->avatar) {
//            return $this->getThumbFileUrl('avatar', 'admin');
////            return $this->avatar->getUrl($size);
//
//        } else {
//            return Yii::$app->request->baseUrl.'/img/user2-160x160.jpg';
        return Yii::$app->request->baseUrl.'/img/user/noavatar.jpg';
//        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        return Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }


}
