<?php

namespace frontend\controllers;

use booking\forms\manage\Order\OrderCreateForm;
use booking\repositories\SlotRepository;
use booking\useCases\manage\OrderManageService;
use booking\useCases\manage\SlotManageService;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
    private SlotManageService $slotService;
    private SlotRepository $slotRepository;
    private OrderManageService $orderService;

    public function __construct(                    $id, $module,
                                                    SlotManageService   $slotService,
                                                    SlotRepository      $slotRepository,
                                                    OrderManageService  $orderService,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->slotService = $slotService;
        $this->slotRepository = $slotRepository;
        $this->orderService = $orderService;
    }
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
            'captcha' => [
                'class' => \yii\captcha\CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     *
     * @return mixed
     */
    public function actionIndex(?int $step=1)
    {
        $this->layout = 'blank';
        $calendar=$this->slotRepository->calendarWeekly();
        $orderForm=new OrderCreateForm();
        return $this->render('step'.$step,[
            'calendar'=>$calendar,
            'orderForm'=>$orderForm
        ]);
    }

    /**
     *
     * @return mixed
     */
    public function actionStep1()
    {
        $this->layout = 'blank';
        return $this->render('step1');
    }

    /**
     *
     * @return mixed
     */
    public function actionStep2()
    {
        $this->layout = 'blank';
        return $this->render('step2');
    }

    /**
     *
     * @return mixed
     */
    public function actionStep3()
    {
        $this->layout = 'blank';
        return $this->render('step3');
    }

    /**
     *
     * @return mixed
     */
    public function actionStep4()
    {
        $this->layout = 'blank';
        return $this->render('step4');
    }


}
