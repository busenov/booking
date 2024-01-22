<?php

namespace backend\controllers;

use booking\repositories\SlotRepository;
use booking\useCases\manage\SlotManageService;
use common\models\LoginForm;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

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
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'calendar'],
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

    public function __construct(                  $id, $module,
                                SlotManageService $slotService,
                                SlotRepository    $slotRepository,
                                                  $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->slotService = $slotService;
        $this->slotRepository = $slotRepository;
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
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionCalendar()
    {
        $calendar=$this->slotRepository->getCalendar();
        //если слотов нет на текущее время, тогда генерируем слоты
        if (empty($calendar)) {
            $this->slotService->generateSlots();
            $calendar=$this->slotRepository->getCalendar();
        }
        return $this->render('calendar',[
            'calendar'=>$calendar
        ]);
    }
}
