<?php

namespace app\controllers;

use app\models\User_rol;
use app\models\User_rolSearch;
use Yii;
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
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
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

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
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
        $this->findModel($idrol)->delete();

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
            return [
                'ok' => true,
                'idrol' => $model->idrol
            ];
        }

        return ['ok' => false];
    }
}
