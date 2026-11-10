<?php

namespace app\controllers;

use app\helpers\UserHelper;
use app\models\DocumentacionTipo;
use app\models\PersonaDocumentacion;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DocumentacionTipoController extends Controller
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
                'query' => DocumentacionTipo::find(),
                'sort' => ['defaultOrder' => ['descripcion' => SORT_ASC]],
            ]),
        ]);
    }

    public function actionCreate()
    {
        return $this->formulario(new DocumentacionTipo(), 'create');
    }

    public function actionUpdate($id)
    {
        return $this->formulario($this->findModel($id), 'update');
    }

    public function actionDelete($id)
    {
        $tipo = $this->findModel($id);

        if (PersonaDocumentacion::find()->where(['idtipo' => $tipo->idtipo_documentacion])->exists()) {
            Yii::$app->session->setFlash('error', 'No se puede eliminar un tipo que ya tiene documentos cargados.');
        } else {
            $tipo->delete();
            Yii::$app->session->setFlash('success', 'El tipo de documentación se eliminó.');
        }

        return $this->redirect(['index']);
    }

    private function formulario(DocumentacionTipo $tipo, string $vista)
    {
        if ($tipo->load($this->request->post()) && $tipo->save()) {
            Yii::$app->session->setFlash('success', 'El tipo de documentación se guardó correctamente.');

            return $this->redirect(['index']);
        }

        return $this->render($vista, ['tipo' => $tipo]);
    }

    private function findModel($id): DocumentacionTipo
    {
        $tipo = DocumentacionTipo::findOne((int) $id);

        if ($tipo === null) {
            throw new NotFoundHttpException('El tipo de documentación no existe.');
        }

        return $tipo;
    }
}
