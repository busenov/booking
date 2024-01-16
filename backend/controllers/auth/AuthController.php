<?php
namespace backend\controllers\auth;

use booking\entities\User\User;
use booking\forms\auth\LoginForm;
use booking\useCases\auth\AuthService;
use booking\useCases\auth\SignupService;
use common\auth\Identity;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class AuthController extends Controller
{
    private AuthService $authService;
    private SignupService $signupService;

    public function __construct($id, $module, AuthService $service, SignupService $signupService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->authService = $service;
        $this->signupService = $signupService;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'logout' => ['post'],
//                ],
//            ],
        ];
    }

    /**
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $form = new LoginForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $user = $this->authService->auth($form);
                if (Yii::$app->user->login(new Identity($user), $form->rememberMe ? 3600 * 24 * 30 : 0)) {
                }else {
                    Yii::$app->errorHandler->logException('При авторизации произошла ошибка. Свяжитесь с администратором');
                    Yii::$app->session->setFlash('error', 'При авторизации произошла ошибка. Свяжитесь с администратором');
                }
                return $this->goBack();
            } catch (\DomainException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('login', [
            'model' => $form,
        ]);
    }

    /**
     * @return mixed
     */
    public function actionLogout()
    {

        $prevUserId = Yii::$app->session->get('prev_user_id');
//        dump($prevUserId);exit;
        if (!empty($prevUserId) && null !== ($prevUser = $this->findModel($prevUserId))) {
            Yii::$app->session->remove('prev_user_id');
            Yii::$app->user->switchIdentity($prevUser, 3600 * 24 * 30);
            return $this->reload();
        }

        Yii::$app->user->logout();
        return $this->goHome();
    }

    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    private function reload()
    {
        if (Yii::$app->request->referrer) {
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            return $this->goHome();
        }
    }
}
