<?php

namespace backend\controllers\amocrm;

use backend\forms\AmoCRM\CredentialSearch;
use booking\entities\AmoCRM\Credential;
use booking\entities\Order\Order;
use booking\forms\AmoCRM\CredentialForm;
use booking\forms\AmoCRM\hipsorurzu\LeadPipeline7665106;
use booking\repositories\CredentialRepository;
use booking\useCases\AmoCRM\AmoCRMService;
use booking\useCases\manage\AmoCRM\CredentialManageService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CompanyController implements the CRUD actions for Companies model.
 */
class CredentialController extends Controller
{

    private CredentialManageService $service;

    public function __construct($id, $module,
                                CredentialManageService $service,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
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
                            'allow' => true,
                            'roles' => ['admin'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Widgets models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CredentialSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Widget model.
     * @param int $id $widget_id
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
     * Creates a new Widget model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $form= new CredentialForm();

        if ($this->request->isPost) {
            if ($form->load($this->request->post()) && $form->validate()) {
                $entity=$this->service->create($form);
                Yii::$app->session->setFlash('success', 'Успешно создан доступ:'.$entity->domain);
                return $this->redirect(['view','id'=>$entity->id]);
            }
        }

        return $this->render('create', [
            'model' => $form,
        ]);
    }

    /**
     * Updates an existing Widget model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $entity = $this->findModel($id);
        $form= new CredentialForm($entity);

        if ($this->request->isPost && $form->load($this->request->post())&& $form->validate()) {
            $this->service->edit($id,$form);
            $entity = $this->findModel($id);
            Yii::$app->session->setFlash('success', 'Успешно отредактирован доступ: '.$entity->domain);
            return $this->redirect(['view', 'id' => $entity->id]);
        }

        return $this->render('update', [
            'model' => $form,
        ]);
    }

    /**
     * Deletes an existing Companies model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $entity = $this->findModel($id);
        try {
            $this->service->remove($id);
        } catch (\DomainException $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        Yii::$app->session->setFlash('success', 'Успешно удален доступ: '.$entity->domain);
        return $this->redirect(['index']);
    }

    public function actionTest()
    {
        $amocrm=new AmoCRMService();
        $credential=Credential::findOne(Credential::MAIN_ID);
        $amocrm->setCredential($credential);

        $order=Order::findOne(99);
        $amocrm->addLead(new LeadPipeline7665106($order));
        return 'tut';
    }
    /**
     * Finds the Companies model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @return Credential the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CredentialRepository::find_st($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
