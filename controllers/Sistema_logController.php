<?php

namespace app\controllers;

use app\models\SistemaLog;
use app\models\SistemaLogSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class Sistema_logController  extends Controller
{
    public function actionIndex_old()
    {
        $query = SistemaLog::find()
            ->with(['usuario'])
            ->orderBy(['fecha' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

        public function actionIndex()
    {
        $searchModel = new SistemaLogSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return [

                    'forceReload' => '#user-pjax-container',
                    'title' => "Creado con éxito",
                    'content' => $this->renderAjax('view', ['model' => $model]),
                    'footer' => '<div class="d-flex justify-content-center w-100">' .
                        Html::button('Cerrar', [
                            'class' => 'btn-custom',
                            'data-bs-dismiss' => 'modal'
                        ]) .
                        '</div>',
                ];
            
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = SistemaLog::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('El log no existe.');
    }
}