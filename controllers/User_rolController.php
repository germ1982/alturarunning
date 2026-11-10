<?php

namespace app\controllers;

use app\helpers\UserHelper;
use app\models\SistemaLog;
use app\models\User_rol;
use app\models\User_rolSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * User_rolController implements the CRUD actions for User_rol model.
 */
class User_rolController extends Controller
{
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
                            'matchCallback' => static fn () => UserHelper::esAdmin(),
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                        'guardar_rol' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all User_rol models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new User_rolSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User_rol model.
     * @param int $idrol
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($idrol)
    {
        return $this->render('view', [
            'model' => $this->findModel($idrol),
        ]);
    }

    /**
     * Creates a new User_rol model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new User_rol();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                SistemaLog::registrar(SistemaLog::MODULO_ROLES, SistemaLog::ACCION_CREATE, $model->idrol, 'Se creó el rol ' . $model->nombre);

                return $this->redirect(['view', 'idrol' => $model->idrol]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User_rol model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $idrol
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($idrol)
    {
        $model = $this->findModel($idrol);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->validate()) {
            $cambios = SistemaLog::diferencias($model, ['nombre' => 'Nombre', 'descripcion' => 'Descripción', 'activo' => 'Activo']);
            $model->save(false);
            SistemaLog::registrar(SistemaLog::MODULO_ROLES, SistemaLog::ACCION_UPDATE, $model->idrol, SistemaLog::conDetalle('Se editó el rol ' . $model->nombre, $cambios));

            return $this->redirect(['view', 'idrol' => $model->idrol]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User_rol model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $idrol
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($idrol)
    {
        $model = $this->findModel($idrol);
        $model->delete();
        SistemaLog::registrar(SistemaLog::MODULO_ROLES, SistemaLog::ACCION_DELETE, (int) $idrol, 'Se eliminó el rol ' . $model->nombre);

        return $this->redirect(['index']);
    }

    /**
     * Finds the User_rol model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $idrol
     * @return User_rol the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($idrol)
    {
        if (($model = User_rol::findOne(['idrol' => $idrol])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionGuardar_rol()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $model = new User_rol();
        $model->nombre = Yii::$app->request->post('nombre');
        $model->descripcion = Yii::$app->request->post('descripcion');

        if ($model->save()) {
            SistemaLog::registrar(SistemaLog::MODULO_ROLES, SistemaLog::ACCION_CREATE, $model->idrol, 'Se creó el rol ' . $model->nombre);

            return [
                'ok' => true,
                'idrol' => $model->idrol
            ];
        }

        return ['ok' => false];
    }
}
