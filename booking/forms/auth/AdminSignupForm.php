<?php
namespace booking\forms\auth;

use Yii;
use yii\base\Model;

/**
 * Signup form
 *
 */
class AdminSignupForm extends Model
{
    public $name;
    public $surname;
    public $email;
    public $password;
    public $password_repeat;

    public bool $agreeTerm=true;

    public $reCaptcha;

    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['name', 'trim'],
            ['name', 'required'],
            ['name', 'string', 'min' => 2, 'max' => 255],

            ['surname', 'trim'],
            ['surname', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\rent\entities\User\User', 'message' => 'Email уже используется'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
            ['password_repeat', 'required'],
            ['password_repeat', 'compare', 'compareAttribute'=>'password', 'message'=>"Пароли не одинаковые" ],

//            [['reCaptcha'], ReCaptchaValidator3::class,
//                'secret' => isset(Yii::$app->settings->reCaptcha->google_secretV3)?Yii::$app->settings->reCaptcha->google_secretV3:'test', // unnecessary if reСaptcha is already configured
//                'threshold' => 0.5,
//                'action' => 'signup',
//                'when' => function() {return (YII_ENV_PROD and Yii::$app->settings->reCaptcha->google_siteKeyV3);}
//            ],

            ['agreeTerm', 'required'],
            ['agreeTerm', 'boolean'],

        ];
    }
    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'surname' => 'Фамилия',
            'email' => 'Email',
            'password' => 'Пароль',
            'password_repeat' => 'Повтор пароля',
        ];
    }

}
