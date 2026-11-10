<?php

namespace app\controllers;

use app\helpers\UserHelper;
use app\models\SistemaLog;
use app\models\SistemaLogSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Consulta de la auditoría. Es de solo lectura: un registro que se puede borrar no sirve de prueba.
 */
class Sistema_logController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => static fn () => UserHelper::puede('auditorias'),
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new SistemaLogSearch();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->search($this->request->queryParams),
        ]);
    }

    public function actionView($id)
    {
        $model = SistemaLog::findOne((int) $id);

        if ($model === null) {
            throw new NotFoundHttpException('El registro no existe.');
        }

        return $this->render('view', ['model' => $model]);
    }
}
