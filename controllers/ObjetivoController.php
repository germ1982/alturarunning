<?php

namespace app\controllers;

use app\helpers\UserHelper;
use app\models\Objetivo;
use app\models\ObjetivoDesarrollo;
use app\models\SistemaLog;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ObjetivoController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => static fn () => UserHelper::puede('alumnos'),
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['delete' => ['POST']],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'dataProvider' => new ActiveDataProvider([
                'query' => Objetivo::find(),
                'sort' => ['defaultOrder' => ['descripcion' => SORT_ASC]],
            ]),
        ]);
    }

    public function actionCreate()
    {
        return $this->formulario(new Objetivo(['puntaje' => 0]), 'create');
    }

    public function actionUpdate($id)
    {
        return $this->formulario($this->findModel($id), 'update');
    }

    public function actionDelete($id)
    {
        $objetivo = $this->findModel($id);

        if (ObjetivoDesarrollo::find()->where(['idobjetivo' => $objetivo->idobjetivo])->exists()) {
            Yii::$app->session->setFlash('error', 'No se puede eliminar un objetivo asignado a alumnos.');
        } else {
            $objetivo->delete();
            SistemaLog::registrar(SistemaLog::MODULO_OBJETIVOS, SistemaLog::ACCION_DELETE, $objetivo->idobjetivo, 'Se eliminó el objetivo ' . $objetivo->descripcion);
            Yii::$app->session->setFlash('success', 'El objetivo se eliminó correctamente.');
        }

        return $this->redirect(['index']);
    }

    private function formulario(Objetivo $objetivo, string $vista)
    {
        if ($objetivo->load($this->request->post()) && $objetivo->validate()) {
            $esNuevo = $objetivo->isNewRecord;
            $cambios = SistemaLog::diferencias($objetivo, ['descripcion' => 'Descripción', 'puntaje' => 'Puntaje']);

            $objetivo->save(false);
            SistemaLog::registrar(
                SistemaLog::MODULO_OBJETIVOS,
                $esNuevo ? SistemaLog::ACCION_CREATE : SistemaLog::ACCION_UPDATE,
                $objetivo->idobjetivo,
                $esNuevo ? 'Se creó el objetivo ' . $objetivo->descripcion . ' (' . $objetivo->puntaje . ' pts)' : SistemaLog::conDetalle('Se editó el objetivo ' . $objetivo->descripcion, $cambios)
            );
            Yii::$app->session->setFlash('success', 'El objetivo se guardó correctamente.');

            return $this->redirect(['index']);
        }

        return $this->render($vista, ['objetivo' => $objetivo]);
    }

    private function findModel($id): Objetivo
    {
        $objetivo = Objetivo::findOne((int) $id);

        if ($objetivo === null) {
            throw new NotFoundHttpException('El objetivo no existe.');
        }

        return $objetivo;
    }
}
