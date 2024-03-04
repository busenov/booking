<?php

namespace frontend\controllers;

use booking\entities\Order\Order;
use booking\entities\Order\OrderItem;
use booking\entities\Slot\Slot;
use booking\forms\manage\Order\CustomerForm;
use booking\forms\manage\Order\LicenseForm;
use booking\forms\manage\Order\OrderCreateForm;
use booking\forms\manage\Order\OrderEditForm;
use booking\forms\manage\Order\RacersForm;
use booking\forms\manage\Order\SlotCreateForm;
use booking\helpers\DateHelper;
use booking\repositories\LicenseRepository;
use booking\repositories\OrderRepository;
use booking\repositories\SlotRepository;
use booking\useCases\manage\OrderManageService;
use booking\useCases\manage\SlotManageService;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use PharIo\Version\Exception;
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
use yii\web\Cookie;
use yii\web\NotFoundHttpException;
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
    private OrderRepository $orderRepository;
    private LicenseRepository $licenseRepository;

    public function __construct(                    $id, $module,
                                                    SlotManageService   $slotService,
                                                    SlotRepository      $slotRepository,
                                                    OrderManageService  $orderService,
                                                    OrderRepository     $orderRepository,
                                                    LicenseRepository   $licenseRepository,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->slotService = $slotService;
        $this->slotRepository = $slotRepository;
        $this->orderService = $orderService;
        $this->orderRepository = $orderRepository;
        $this->licenseRepository = $licenseRepository;
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

        $order=null;

        $racersForm=null;
        $licenseForm=null;
        if ($orderGuid=Yii::$app->request->cookies->get(Order::COOKIE_NAME_GUID)){
            $order=$this->findOrder($orderGuid);
            $racersForm = new RacersForm($order);
        }
        if (empty($order)and $step!==1) {
            return $this->redirect(['index','step'=>1]);
        }
        if ($order) {
            $customerOrder=new CustomerForm($order->customer);
        } else {
            $customerOrder=new CustomerForm();
        }

        if ($step==1) {
            $licenseForm=new LicenseForm(($order and $order->customer)?$order->customer->license:null);
        } elseif ($step==2) {
            if ($this->request->isPost && $customerOrder->load($this->request->post())) {
//                dump($customerOrder);
                $this->orderService->checkout($order,$customerOrder);
//                exit;
                return $this->redirect(['index','step'=>3]);
            }
        } elseif ($step==4) {
            if ($this->request->isPost) {
                if ($racersForm->load($this->request->post())) {
                    try {
                        $this->orderService->addAdditionalInfo($order,$racersForm);
                        Yii::$app->session->setFlash('success', 'Данные успешно сохранены.');
                    }catch (Exception $ex) {
                        Yii::$app->session->setFlash('error', 'Ошибка при сохранение данных: '. $ex->getMessage());
                    }

                }
            }
        }

        return $this->render('step'.$step,[
            'calendar'=>$calendar,
            'order'=>$order,
            'customerOrder'=>$customerOrder,
            'racersForm'=>$racersForm,
            'licenseForm'=>$licenseForm
        ]);
    }
    /**
     * Шаг1. Генерируем модального окно заказал по Слоту
     * @param int $slot_id
     * @return array|Response
     * @throws NotFoundHttpException
     */
    public function actionOrderModalAjax(int $slot_id)
    {
//        \Yii::$app->response->format = Response::FORMAT_JSON;
        $order=null;
        try {
            if ($orderGuid=Yii::$app->request->cookies->get(Order::COOKIE_NAME_GUID)) {
                $order = $this->findOrder($orderGuid->value);
                $orderForm=new SlotCreateForm($slot_id,$order);
            } else {
                $orderForm=new SlotCreateForm($slot_id);
            }
            $orderForm->slot_id=$slot_id;



            $slot=$this->findSlot($slot_id);
            $data=$this->renderAjax('_orderModal',[
                'order'=>$order,
                'slot'=>$slot,
                'orderForm'=>$orderForm
            ]);
            return $this->asJson(['status'=>'success','data'=>$data]);
        } catch (\DomainException $ex) {
            return ['status'=>'error','data'=>$ex->getMessage()];
        }


    }

    /**
     * Добавляем(изменяем) позицию в заказ
     * @param int|null $slot_id
     * @return array|string[]
     * @throws NotFoundHttpException
     */
    public function actionAddToOrderAjax(?int $slot_id=null)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $order=null;
        if ($orderGuid=Yii::$app->request->cookies->get(Order::COOKIE_NAME_GUID)){
            $order=$this->findOrder($orderGuid->value);

            $form=new SlotCreateForm($slot_id,$order);
        } else {
            $form=new SlotCreateForm($slot_id);
        }


        if ($this->request->isPost && $form->load($this->request->post())) {

            $order=$this->orderService->addToOrder($form);
            $cookies=Yii::$app->response->cookies;
            $cookies->add(new Cookie([
                'name' => Order::COOKIE_NAME_GUID,
                'value' => $order->guid,
                'expire'=> time() + 60*60*24
            ]));
            return ['status'=>'success', 'order_guid'=>$order->guid,'order'=>$order->toJs()];
        } else {
            return ['status'=>'error'];
        }
    }

    /**
     * @param int $item
     * @param int $qty
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionChangeQtyToOrderAjax(int $item, int $qty): Response
    {
        $item = $this->findOrderItem($item);

        try {
            if (!(
                $orderGuid=Yii::$app->request->cookies->get(Order::COOKIE_NAME_GUID) AND
                $order=$this->findOrder($orderGuid->value) AND
                $order->hasItem($item)
            )){
                throw new NotFoundHttpException('The requested page does not exist.');
            }
            if ($order = $this->orderService->changeItem($item,$qty)) {
                return $this->asJson(['status'=>'success', 'order'=>$order->toJs()]);
            } else {
                return $this->asJson(['status'=>'error']);
            }

        } catch (Exception $ex) {
            return  $this->asJson(['status'=>'error', 'msg'=>$ex->getMessage()]);
        }
    }
    public function actionDeleteItem(int $item,int $step=2):Response
    {
        $item = $this->findOrderItem($item);
        try {
            if (!(
                $orderGuid=Yii::$app->request->cookies->get(Order::COOKIE_NAME_GUID) AND
                $order=$this->findOrder($orderGuid->value) AND
                $order->hasItem($item)
            )){
                throw new NotFoundHttpException('The requested page does not exist.');
            }
            $this->orderService->removeItem($order,$item->id);
            Yii::$app->session->setFlash('success', 'Позиция в заезде успешно удалена.');
        }catch (Exception $ex) {
            Yii::$app->session->setFlash('error', 'Ошибка удаление позиции в заезде: '. $ex->getMessage());
        }
        return $this->redirect(['index','step'=>$step]);
    }
    public function actionDeleteSlot(int $slot,int $step=2):Response
    {
        $slot = $this->findSlot($slot);
        try {
            if (!(
                $orderGuid=Yii::$app->request->cookies->get(Order::COOKIE_NAME_GUID) AND
                $order=$this->findOrder($orderGuid->value)
            )){
                throw new NotFoundHttpException('The requested page does not exist.');
            }
            $this->orderService->removeSlot($order,$slot->id);
            Yii::$app->session->setFlash('success', 'Заезд успешно удален.');
        }catch (Exception $ex) {
            Yii::$app->session->setFlash('error', 'Ошибка удаление заезда: '. $ex->getMessage());
        }
        return $this->redirect(['index','step'=>$step]);
    }

    /**
     * Получаем календарь, где week - это дата в unixtime формате недели
     * @param int $week
     * @return Response
     */
    public function actionGetCalendarAjax(?int $week=null):Response
    {
        $calendar=$this->slotRepository->calendarWeekly($week);
        return $this->asJson([
            'status'=>'success',
            'html'=>$this->renderPartial('_week_dates',['calendar'=>$calendar,'week'=>$week]),
            'calendar'=>json_encode($calendar),
            'month'=>DateHelper::getMonthRu(date('n',$week)-1).' '.date('Y',$week)
        ]);
    }
    public function actionCheckLicense():Response
    {
        try {
            if ($orderGuid=Yii::$app->request->cookies->get(Order::COOKIE_NAME_GUID)) {
                $order=$this->findOrder($orderGuid->value);
            } else {
                $order=$this->orderService->createEmpty();
                $cookies=Yii::$app->response->cookies;
                $cookies->add(new Cookie([
                    'name' => Order::COOKIE_NAME_GUID,
                    'value' => $order->guid,
                    'expire'=> time() + 60*60*24
                ]));
            }
            $form=new LicenseForm();
            if ($this->request->isPost && $form->load($this->request->post())) {
                if ($this->orderService->checkLicense($form,$order)) {
                    return  $this->asJson(['status'=>'success']);
                }
            }

        } catch (Exception $ex) {
            return  $this->asJson(['status'=>'error', 'msg'=>$ex->getMessage()]);
        }
        return  $this->asJson(['status'=>'error', 'msg'=>'С таким номером лицензия не найдена']);
    }
###
    /**
     * Finds the Teams model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $orderGuid
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findOrder(string $orderGuid):Order
    {
        if (($model = $this->orderRepository->get($orderGuid)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    protected function findSlot(int $slot_id):Slot
    {
        if (($model = $this->slotRepository->get($slot_id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    protected function findOrderItem(int $orderItemId):OrderItem
    {
        if (($model = $this->orderRepository->getItem($orderItemId)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

//    private function checkOrderAndItem(int $orderItemId)
//    {
//
//    }
}
