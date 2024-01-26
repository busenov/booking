<?php

namespace backend\controllers;

use artel\entities\Team\Team;
use app\forms\TeamSearch;
use artel\forms\manage\Team\TeamForm;
use artel\forms\manage\User\AssignUserForm;
use artel\repositories\TeamRepository;
use artel\useCases\manage\TeamManageService;
use backend\forms\CarTypeSearch;
use backend\forms\SlotSearch;
use booking\entities\Car\CarType;
use booking\entities\Slot\Slot;
use booking\forms\manage\Car\CarTypeForm;
use booking\forms\manage\Schedule\ScheduleForm;
use booking\forms\manage\Slot\GenerateForm;
use booking\forms\manage\Slot\SlotForm;
use booking\repositories\CarTypeRepository;
use booking\repositories\ScheduleRepository;
use booking\repositories\SlotRepository;
use booking\useCases\manage\CarTypeManageService;
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
class SlotController extends Controller
{

    private SlotManageService $service;
    private SlotRepository $repository;
    private ScheduleRepository $scheduleRepository;


    public function __construct($id, $module,
                                SlotManageService $service,
                                SlotRepository $repository,
                                ScheduleRepository $scheduleRepository,
                                $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
        $this->repository = $repository;
        $this->scheduleRepository = $scheduleRepository;
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
     */
    public function actionIndex()
    {
        $searchModel = new SlotSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $schedule=$this->scheduleRepository->findByDate($searchModel->period);
        $scheduleForm = new GenerateForm($schedule);


        if ($this->request->isPost){
            $post=$this->request->post();
            if ($post['hasEditable']){
                \Yii::$app->response->format = Response::FORMAT_JSON;
                $slot=$this->findModel(intval($post['editableKey']));
                $form=new SlotForm($slot);

                $attributeName = array_key_first($post[$form->formName()]);
                $oldValue = $form->$attributeName;

                if (($form->load($post)) and ($form->validate())) {
                    try {
                        $this->service->edit($slot,$form);
                        return ['output' => $form->getValue($attributeName), 'message' => ''];
                    } catch (\RuntimeException $e) {
                        return ['output' => $oldValue, 'message' => $e->getMessage()];
                    }
                }
                return ['output' => $oldValue, 'message' => $form->getFirstError($attributeName)];
            }
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'scheduleForm'=>$scheduleForm
        ]);
    }
    public function actionGenerateSlots(int $unixTime)
    {
        $form=new GenerateForm();
        if ($this->request->isPost) {
            if ($form->load($this->request->post()) and $form->validate()) {
                try {
                    $qty=$this->service->generateSlotsForDay($unixTime,$form);
                    Yii::$app->session->setFlash('success', 'Успешно добавлено заездов: '.$qty);
                } catch (\RuntimeException $e) {
                    Yii::$app->session->setFlash('error', 'Ошибка при добавление заездов: '.$e->getMessage());
                }
                return $this->redirect(['index','SlotSearch[period]'=>$unixTime]);
            }
        }
    }
    public function actionChangeStatusAll(int $unixTime,int $status)
    {
        try {
            $qty=$this->service->changeStatusByDay($unixTime,$status);
            Yii::$app->session->setFlash('success', 'Успешно изменены статусы: '.$qty);
        } catch (\RuntimeException $e) {
            Yii::$app->session->setFlash('error', 'Ошибка при изменения статусов: '.$e->getMessage());
        }
        return $this->redirect(['index','SlotSearch[period]'=>$unixTime]);
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
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $form = new SlotForm();

        if ($this->request->isPost) {
            if ($form->load($this->request->post()) ) {
                $entity=$this->service->create($form);
                return $this->redirect(['view', 'id' => $entity->id]);
            }
        }

        return $this->render('create', [
            'model' => $form,
        ]);
    }

    /**
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $entity = $this->findModel($id);
        $form= new SlotForm($entity);

        if ($this->request->isPost && $form->load($this->request->post())) {
            try {
                $this->service->edit($entity,$form);
                $entity = $this->findModel($id);
                Yii::$app->session->setFlash('success', 'Успешно отредактирована запись: '.$entity->getName());
            } catch (\Exception $ex) {
                Yii::$app->session->setFlash('error', 'Ошибка при редактирования записи: '.$entity->getName() . ' '. $ex->getMessage());
            }


            return $this->redirect(['view', 'id' => $entity->id]);
        }

        return $this->render('update', [
            'model' => $form,
        ]);
    }
    public function actionClear(?int $unixTime=null)
    {
        try {
            $this->service->clear($unixTime);
            Yii::$app->session->setFlash('success', 'Успешно прошла очистка');
        } catch (\Exception $ex) {
            Yii::$app->session->setFlash('error', 'Ошибка при очистке: '. $ex->getMessage());
        }
        return $this->redirect(['index']);
    }
    public function actionClearAjax(?int $unixTime=null,bool $force=false)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $this->service->clear($unixTime,$force);
            Yii::$app->session->setFlash('success', 'Успешно прошла очистка');
            return ['status'=>'success'];
        } catch (\Exception $ex) {
            Yii::$app->session->setFlash('error', 'Ошибка при очистке: '. $ex->getMessage());
            return ['status'=>'error','error'=>$ex->getMessage()];
        }
    }
    public function actionClearDay(int $unixTime=null,bool $force=false)
    {
        try {
            $this->service->clear($unixTime,$force);
            Yii::$app->session->setFlash('success', 'Успешно прошла очистка');
        } catch (\Exception $ex) {
            Yii::$app->session->setFlash('error', 'Ошибка при очистке: '. $ex->getMessage());
        }
        return $this->redirect(['index','SlotSearch[period]'=>$unixTime]);
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
            Yii::$app->session->setFlash('success', 'Успешно удалена запись: '.$entity->getName());
        } catch (\Exception $ex) {
            Yii::$app->session->setFlash('error', 'Ошибка при удалении записи: '.$entity->getName() . ' '. $ex->getMessage());
        }
        return $this->redirect(['index']);
    }
    public function actionDeleteHard($id)
    {
        $entity = $this->findModel($id);
        try {
            $this->service->removeHard($entity);
            Yii::$app->session->setFlash('success', 'Успешно удалена запись: '.$entity->getName());
        } catch (\Exception $ex) {
            Yii::$app->session->setFlash('error', 'Ошибка при удалении записи: '.$entity->getName() . ' '. $ex->getMessage());
        }
        return $this->redirect(['index']);
    }

    /**
     * Генерируем слоты на день $unixTime
     * @param int|null $unixTime
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionGenerate(?int $unixTime=null)
    {
        $qty = $this->service->generateSlots(($unixTime?:time()));
        Yii::$app->session->setFlash('success', 'Успешно сгенерировано заездов: '. $qty);
        return $this->redirect(['index','SlotSearch[period]'=>$unixTime]);
    }
    public function actionGenerateAjax(?int $unixTime=null)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $this->service->generateSlots(($unixTime?:time()));
            Yii::$app->session->setFlash('success', 'Cгенерированы слоты на день: '.date('d.m.Y', $unixTime));
            return ['status'=>'success'];
        } catch (\Exception $ex) {
            Yii::$app->session->setFlash('error', 'Ошибка при создании слотов: '.$ex->getMessage());
            return ['status'=>'error','error'=>$ex->getMessage()];
        }
    }

    /**
     * Finds the Teams model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Slot the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Slot::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
