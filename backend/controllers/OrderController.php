<?php

namespace backend\controllers;

use backend\forms\CarTypeSearch;
use backend\forms\OrderSearch;
use backend\forms\SlotSearch;
use booking\entities\Car\CarType;
use booking\entities\Order\Order;
use booking\entities\Slot\Slot;
use booking\forms\manage\Car\CarTypeForm;
use booking\forms\manage\Order\OrderCreateForm;
use booking\forms\manage\Order\OrderEditForm;
use booking\forms\manage\Slot\SlotForm;
use booking\repositories\CarTypeRepository;
use booking\repositories\OrderRepository;
use booking\repositories\SlotRepository;
use booking\useCases\manage\CarTypeManageService;
use booking\useCases\manage\OrderManageService;
use booking\useCases\manage\SlotManageService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 */
class OrderController extends Controller
{

    private OrderManageService $service;
    private OrderRepository $repository;

    public function __construct($id, $module,
                                OrderManageService $service,
                                OrderRepository $repository,
                                $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
        $this->repository = $repository;
    }
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'actions' => ['index','view'],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                        [
//                            'actions' => ['logout', 'index','docs'],
                            'allow' => true,
                            'roles' => ['admin'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                        'delete-hard' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' =>$model,
        ]);
    }

    /**
     * Сколько можно заказать машин в заезде, если $car указано, тогда именно этого типа машин
     * @param int $slot
     * @param int|null $car
     * @return int
     * @throws NotFoundHttpException
     */
    public function actionGetFreeCarAjax(int $slot, int $car=null)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $slot=$this->findSlot($slot);
        return $slot->getFree($car);
    }

    /**
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $entity = $this->findModel($id);
        try {
            $this->service->remove($entity);
            Yii::$app->session->setFlash('success', 'Успешно удалена запись: '.$entity->id);
        } catch (\Exception $ex) {
            Yii::$app->session->setFlash('error', 'Ошибка при удалении записи: '.$entity->id . ' '. $ex->getMessage());
        }
        return $this->redirect(['index']);
    }
    public function actionDeleteHard($id)
    {
        $entity = $this->findModel($id);
        try {
            $this->service->removeHard($entity);
            Yii::$app->session->setFlash('success', 'Успешно удалена запись: '.$entity->id);
        } catch (\Exception $ex) {
            Yii::$app->session->setFlash('error', 'Ошибка при удалении записи: '.$entity->id . ' '. $ex->getMessage());
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the Teams model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    /**
     * Finds the Teams model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Slot the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findSlot($slotId)
    {
        if (($model = Slot::findOne(['id' => $slotId])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
