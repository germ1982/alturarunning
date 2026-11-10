<?php

namespace app\controllers;

use app\helpers\UserHelper;
use app\models\Alumno;
use app\models\ConstantesGlobales;
use app\models\Cuota;
use app\models\CuotaPago;
use app\models\SistemaLog;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class CuotaController extends Controller
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
                'actions' => ['delete' => ['POST'], 'generar' => ['POST']],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'dataProvider' => new ActiveDataProvider([
                'query' => Cuota::find()->with('pagos'),
                'sort' => ['defaultOrder' => ['mes' => SORT_DESC, 'tipo' => SORT_ASC]],
            ]),
        ]);
    }

    public function actionCreate()
    {
        return $this->formulario(new Cuota(['tipo' => ConstantesGlobales::CUOTA_CLASE_NORMAL, 'mes' => date('Y-m')]), 'create');
    }

    public function actionUpdate($id)
    {
        return $this->formulario($this->findModel($id), 'update');
    }

    public function actionDelete($id)
    {
        $cuota = $this->findModel($id);

        if ($cuota->pagos !== []) {
            Yii::$app->session->setFlash('error', 'No se puede eliminar una cuota que ya tiene pagos cargados.');
        } else {
            $cuota->delete();
            SistemaLog::registrar(SistemaLog::MODULO_CUOTAS, SistemaLog::ACCION_DELETE, $cuota->idcuota, 'Se eliminó la cuota ' . $cuota->getEtiqueta());
            Yii::$app->session->setFlash('success', 'La cuota se eliminó correctamente.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Crea un pago pendiente para cada alumno activo que todavía no tenga esta cuota.
     */
    public function actionGenerar($id)
    {
        $cuota = $this->findModel($id);

        if ((int) $cuota->tipo !== ConstantesGlobales::CUOTA_CLASE_NORMAL) {
            Yii::$app->session->setFlash('error', 'Las clases personalizadas se cargan desde cada alumno.');

            return $this->redirect(['index']);
        }

        $faltantes = Alumno::find()
            ->where(['estado' => ConstantesGlobales::ALUMNO_ACTIVO])
            ->andWhere(['not in', 'idalumno', CuotaPago::find()->select('idalumno')->where(['idcuota' => $cuota->idcuota])])
            ->select('idalumno')
            ->column();

        $filas = array_map(static fn ($idalumno) => [$cuota->idcuota, $idalumno, $cuota->monto, ConstantesGlobales::CUOTA_PENDIENTE], $faltantes);

        if ($filas !== []) {
            Yii::$app->db->createCommand()
                ->batchInsert(CuotaPago::tableName(), ['idcuota', 'idalumno', 'monto', 'estado_pago'], $filas)
                ->execute();

            SistemaLog::registrar(SistemaLog::MODULO_CUOTAS, SistemaLog::ACCION_CREATE, $cuota->idcuota, 'Se generaron ' . count($filas) . ' pagos pendientes de ' . $cuota->getEtiqueta());
        }

        Yii::$app->session->setFlash('success', 'Se generaron ' . count($filas) . ' pagos pendientes para ' . $cuota->getMesTexto() . '.');

        return $this->redirect(['index']);
    }

    private function formulario(Cuota $cuota, string $vista)
    {
        if ($cuota->load($this->request->post()) && $cuota->validate()) {
            $esNueva = $cuota->isNewRecord;
            $cambios = SistemaLog::diferencias($cuota, [
                'mes' => 'Mes',
                'tipo' => ['Tipo', ConstantesGlobales::TIPOS_CUOTA],
                'monto' => 'Monto',
            ]);

            $cuota->save(false);
            SistemaLog::registrar(
                SistemaLog::MODULO_CUOTAS,
                $esNueva ? SistemaLog::ACCION_CREATE : SistemaLog::ACCION_UPDATE,
                $cuota->idcuota,
                $esNueva ? 'Se creó la cuota ' . $cuota->getEtiqueta() : SistemaLog::conDetalle('Se editó la cuota ' . $cuota->getMesTexto(), $cambios)
            );
            Yii::$app->session->setFlash('success', 'La cuota se guardó correctamente.');

            return $this->redirect(['index']);
        }

        return $this->render($vista, ['cuota' => $cuota]);
    }

    private function findModel($id): Cuota
    {
        $cuota = Cuota::findOne((int) $id);

        if ($cuota === null) {
            throw new NotFoundHttpException('La cuota no existe.');
        }

        return $cuota;
    }
}
