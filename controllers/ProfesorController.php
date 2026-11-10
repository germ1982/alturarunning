<?php

namespace app\controllers;

use app\helpers\UserHelper;
use app\models\Calendario;
use app\models\ConstantesGlobales;
use app\models\DatoSistema;
use app\models\Persona;
use app\models\PersonaInstructor;
use app\models\ProfesorForm;
use app\models\SistemaLog;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ProfesorController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => static fn () => UserHelper::puede('profesores'),
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
        $profesores = PersonaInstructor::find()
            ->joinWith('persona')
            ->with('persona.fotoPrincipal')
            ->orderBy(['persona.apellido' => SORT_ASC, 'persona.nombre' => SORT_ASC])
            ->all();

        $horarios = Calendario::find()
            ->select(['COUNT(*)', 'idinstructor'])
            ->groupBy('idinstructor')
            ->indexBy('idinstructor')
            ->column();

        return $this->render('index', ['profesores' => $profesores, 'horarios' => $horarios]);
    }

    public function actionCreate()
    {
        return $this->formulario(new PersonaInstructor(), 'create');
    }

    public function actionUpdate($id)
    {
        return $this->formulario($this->findModel($id), 'update');
    }

    public function actionDelete($id)
    {
        $profesor = $this->findModel($id);
        $nombre = $profesor->persona->getNombreCompleto();

        if (Calendario::find()->where(['idinstructor' => $profesor->idinstructor])->exists()) {
            Yii::$app->session->setFlash('error', 'No se puede eliminar a ' . $nombre . ' porque tiene horarios asignados. Cambialos primero en Contenidos > Horarios.');
        } else {
            $profesor->delete();
            SistemaLog::registrar(SistemaLog::MODULO_PROFESORES, SistemaLog::ACCION_DELETE, (int) $id, 'Se eliminó al profesor ' . $nombre);
            Yii::$app->session->setFlash('success', $nombre . ' ya no figura como profesor. Sus datos personales se conservan.');
        }

        return $this->redirect(['index']);
    }

    private function formulario(PersonaInstructor $profesor, string $vista)
    {
        $modelo = new ProfesorForm($profesor);
        $esNuevo = $profesor->isNewRecord;

        if ($this->request->isPost) {
            $modelo->cargar($this->request->post());

            if ($modelo->guardar()) {
                SistemaLog::registrar(
                    SistemaLog::MODULO_PROFESORES,
                    $esNuevo ? SistemaLog::ACCION_CREATE : SistemaLog::ACCION_UPDATE,
                    $profesor->idinstructor,
                    SistemaLog::conDetalle(($esNuevo ? 'Se dio de alta al profesor ' : 'Se editó al profesor ') . $modelo->persona->getNombreCompleto(), $modelo->cambios)
                );
                Yii::$app->session->setFlash('success', 'El profesor se guardó correctamente.');

                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('error', 'Revisá los errores marcados. Si elegiste una foto nueva, volvé a seleccionarla.');
        }

        $especialidades = DatoSistema::opciones(ConstantesGlobales::TIPO_ESPECIALIDAD_INSTRUCTOR);
        $opcionesEspecialidad = array_combine($especialidades, $especialidades);

        // Si tenía una especialidad que ya no está en la lista, se mantiene como opción.
        if ($profesor->especialidad !== null && !isset($opcionesEspecialidad[$profesor->especialidad])) {
            $opcionesEspecialidad[$profesor->especialidad] = $profesor->especialidad;
        }

        return $this->render($vista, [
            'modelo' => $modelo,
            'especialidades' => $opcionesEspecialidad,
            'personasLibres' => $esNuevo ? $this->personasSinProfesor() : [],
        ]);
    }

    private function personasSinProfesor(): array
    {
        $opciones = [];
        $personas = Persona::find()
            ->where(['not in', 'idpersona', PersonaInstructor::find()->select('idpersona')])
            ->orderBy(['apellido' => SORT_ASC, 'nombre' => SORT_ASC])
            ->all();

        foreach ($personas as $persona) {
            $opciones[$persona->idpersona] = $persona->apellido . ', ' . $persona->nombre . ' (' . $persona->documento . ')';
        }

        return $opciones;
    }

    private function findModel($id): PersonaInstructor
    {
        $profesor = PersonaInstructor::findOne((int) $id);

        if ($profesor === null) {
            throw new NotFoundHttpException('El profesor no existe.');
        }

        return $profesor;
    }
}
