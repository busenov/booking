<?php

namespace backend\controllers;

use artel\entities\Team\Team;
use app\forms\TeamSearch;
use artel\forms\manage\Team\TeamForm;
use artel\forms\manage\User\AssignUserForm;
use artel\repositories\TeamRepository;
use artel\useCases\manage\TeamManageService;
use backend\forms\CarTypeSearch;
use backend\forms\UserSearch;
use booking\entities\Car\CarType;
use booking\entities\User\User;
use booking\forms\manage\Car\CarTypeForm;
use booking\forms\manage\User\UserCreateForm;
use booking\forms\manage\User\UserEditForm;
use booking\repositories\CarTypeRepository;
use booking\repositories\UserRepository;
use booking\useCases\manage\CarTypeManageService;
use booking\useCases\manage\UserManageService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 */
class UserController extends Controller
{

    private UserManageService $service;
    private UserRepository $repository;

    public function __construct($id, $module,
                                UserManageService $service,
                                UserRepository $repository,
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
        $searchModel = new UserSearch();
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
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $form = new UserCreateForm();

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
        $form= new UserEditForm($entity);

        if ($this->request->isPost && $form->load($this->request->post())) {
            try {
                $this->service->edit($entity,$form);
                $entity = $this->findModel($id);
                Yii::$app->session->setFlash('success', 'Успешно отредактирована запись: '.$entity->name);
            } catch (\Exception $ex) {
                Yii::$app->session->setFlash('error', 'Ошибка при редактирования записи: '.$entity->name . ' '. $ex->getMessage());
            }


            return $this->redirect(['view', 'id' => $entity->id]);
        }

        return $this->render('update', [
            'model' => $form,
        ]);
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
            Yii::$app->session->setFlash('success', 'Успешно удалена запись: '.$entity->name);
        } catch (\Exception $ex) {
            Yii::$app->session->setFlash('error', 'Ошибка при удалении записи: '.$entity->name . ' '. $ex->getMessage());
        }
        return $this->redirect(['index']);
    }
    public function actionDeleteHard($id)
    {
        $entity = $this->findModel($id);
        try {
            $this->service->removeHard($entity);
            Yii::$app->session->setFlash('success', 'Успешно удалена запись: '.$entity->name);
        } catch (\Exception $ex) {
            Yii::$app->session->setFlash('error', 'Ошибка при удалении записи: '.$entity->name . ' '. $ex->getMessage());
        }
        return $this->redirect(['index']);
    }


    /**
     * Finds the Teams model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return CarType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
