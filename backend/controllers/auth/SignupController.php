<?php
namespace backend\controllers\auth;

use booking\forms\auth\AdminSignupForm;
use booking\forms\auth\LoginForm;
use booking\forms\auth\SignupForm;
use booking\useCases\auth\SignupService;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class SignupController extends Controller
{
    private $service;

    public function __construct($id, $module, SignupService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

//    public function actions()
//    {
//        return [
//            'captcha' => [
//            'class' => 'yii\captcha\CaptchaAction',
//                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
//            ],
//        ];
//    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return mixed
     */
    public function actionRequest()
    {
        $form = new SignupForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->service->signup($form);
                Yii::$app->session->setFlash('success', 'Проверьте вашу эл.почту для дальнейших инструкций.');
                return $this->goHome();
            } catch (\DomainException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

//        return $this->render('request', [
//            'model' => $form,
//        ]);

        return $this->render('../auth/login', [
            'model' =>  new LoginForm(),
            'signup' => $form,
            'focus' => 'signup'
        ]);
    }

    public function actionSignup()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'main-login';

        $form= new AdminSignupForm();

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->service->adminSignup($form);
                Yii::$app->session->setFlash('success', 'Вы успешно зарегистрировались. Вам на почту: '.$form->email.' отправлена ссылка-приглашения для продолжения работы');

                return $this->goHome();
            } catch (\DomainException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('signup', [
            'model' => $form,
        ]);
    }
    /**
     * @param $token
     * @return mixed
     */
    public function actionConfirm($token)
    {
        try {
            $this->service->confirm($token);
            Yii::$app->session->setFlash('success', 'Ваш Email подтвержден, теперь можете авторизоваться');
            return $this->redirect(['auth/auth/login']);
        } catch (\DomainException $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->goHome();
    }


}