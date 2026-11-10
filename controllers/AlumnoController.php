<?php

namespace app\controllers;

use app\helpers\UserHelper;
use app\models\Alumno;
use app\models\AlumnoForm;
use app\models\AlumnoSearch;
use app\models\ConstantesGlobales;
use app\models\Cuota;
use app\models\CuotaPago;
use app\models\DocumentacionTipo;
use app\models\EventoInscripcion;
use app\models\Objetivo;
use app\models\Persona;
use app\models\SistemaLog;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class AlumnoController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'create', 'update', 'delete'],
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

    /**
     * Tarjetas de alumnos. Quien no gestiona alumnos solo ve a los activos y a sí mismo.
     */
    public function actionListado()
    {
        $puedeGestionar = UserHelper::puede('alumnos');
        $propio = Alumno::delUsuarioActual();

        $query = Alumno::find()
            ->joinWith('persona')
            ->with(['persona.fotoPrincipal', 'pagos'])
            ->orderBy(['persona.apellido' => SORT_ASC, 'persona.nombre' => SORT_ASC]);

        if (!$puedeGestionar) {
            $query->where(['or', ['estado' => ConstantesGlobales::ALUMNO_ACTIVO], ['idalumno' => $propio->idalumno ?? 0]]);
        }

        return $this->render('listado', [
            'alumnos' => $query->all(),
            'propio' => $propio,
            'puedeGestionar' => $puedeGestionar,
        ]);
    }

    public function actionVer($id)
    {
        $alumno = $this->findModel($id);
        $puedeGestionar = UserHelper::puede('alumnos');
        $esPropio = (int) (Alumno::delUsuarioActual()->idalumno ?? 0) === (int) $alumno->idalumno;
        $verPrivado = $puedeGestionar || $esPropio;

        if (!$verPrivado && !$alumno->getEstaActivo()) {
            throw new NotFoundHttpException('El alumno no existe.');
        }

        // El público solo ve las carreras confirmadas; el alumno y quien gestiona ven también las solicitudes.
        $carreras = EventoInscripcion::find()
            ->joinWith('desafio.evento')
            ->where(['evento_inscripcion.idpersona' => $alumno->idpersona])
            ->andWhere($verPrivado
                ? ['<>', 'estado_inscripcion', ConstantesGlobales::INSCRIPCION_CANCELADA]
                : ['estado_inscripcion' => ConstantesGlobales::INSCRIPCIONES_CONFIRMADAS])
            ->andWhere(['evento.estado' => ConstantesGlobales::EVENTO_ACTIVO])
            ->orderBy(['evento.fecha_evento' => SORT_DESC])
            ->all();

        return $this->render('ver', [
            'alumno' => $alumno,
            'carreras' => $carreras,
            'puedeGestionar' => $puedeGestionar,
            'verPrivado' => $verPrivado,
            'pagos' => $verPrivado
                ? CuotaPago::find()->joinWith('cuota')->where(['idalumno' => $alumno->idalumno])->orderBy(['cuotas.mes' => SORT_DESC])->all()
                : [],
            'objetivos' => $verPrivado ? $alumno->persona->objetivosDesarrollo : [],
            'documentacion' => $verPrivado ? $alumno->persona->documentacion : [],
            'tiposDocumentacion' => $verPrivado ? DocumentacionTipo::opciones() : [],
        ]);
    }

    public function actionIndex()
    {
        $searchModel = new AlumnoSearch();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->search($this->request->queryParams),
        ]);
    }

    public function actionCreate()
    {
        $alumno = new Alumno(['rol' => ConstantesGlobales::ALUMNO_CORREDOR, 'estado' => ConstantesGlobales::ALUMNO_ACTIVO]);

        return $this->formulario($alumno, SistemaLog::ACCION_CREATE, 'Se dio de alta al alumno ', 'create');
    }

    public function actionUpdate($id)
    {
        return $this->formulario($this->findModel($id), SistemaLog::ACCION_UPDATE, 'Se editó al alumno ', 'update');
    }

    public function actionDelete($id)
    {
        $alumno = $this->findModel($id);

        if ($alumno->pagos !== []) {
            Yii::$app->session->setFlash('error', 'No se puede eliminar un alumno con cuotas cargadas. Podés marcarlo como inactivo.');

            return $this->redirect(['index']);
        }

        $nombre = $alumno->persona->getNombreCompleto();
        $alumno->delete();

        SistemaLog::registrar(SistemaLog::MODULO_MIEMBROS, SistemaLog::ACCION_DELETE, (int) $id, 'Se eliminó al alumno ' . $nombre);
        Yii::$app->session->setFlash('success', 'El alumno se eliminó correctamente. Sus datos personales se conservan.');

        return $this->redirect(['index']);
    }

    private function formulario(Alumno $alumno, int $accion, string $textoLog, string $vista)
    {
        $modelo = new AlumnoForm($alumno);

        if ($this->request->isPost) {
            // Si PHP recortó el POST (max_input_vars), este campo final no llega.
            if ($this->request->post('formulario_completo') !== '1') {
                Yii::$app->session->setFlash('error', 'El formulario era demasiado grande y no se guardó nada. Avisale al administrador del sitio.');
            } else {
                $modelo->cargar($this->request->post());

                if ($modelo->guardar()) {
                    SistemaLog::registrar(SistemaLog::MODULO_MIEMBROS, $accion, $alumno->idalumno, $textoLog . $modelo->persona->getNombreCompleto());
                    Yii::$app->session->setFlash('success', 'El alumno se guardó correctamente.');

                    return $this->redirect(['ver', 'id' => $alumno->idalumno]);
                }

                Yii::$app->session->setFlash('error', 'Revisá los errores marcados. Si elegiste una foto nueva, volvé a seleccionarla.');
            }
        }

        return $this->render($vista, [
            'modelo' => $modelo,
            'cuotas' => $this->opcionesCuotas(),
            'objetivos' => $this->opcionesObjetivos(),
            'personasLibres' => $alumno->isNewRecord ? $this->personasSinAlumno() : [],
        ]);
    }

    private function opcionesCuotas(): array
    {
        $opciones = [];

        foreach (Cuota::find()->orderBy(['mes' => SORT_DESC, 'tipo' => SORT_ASC])->all() as $cuota) {
            $opciones[$cuota->idcuota] = $cuota->getEtiqueta();
        }

        return $opciones;
    }

    private function opcionesObjetivos(): array
    {
        $opciones = [];

        foreach (Objetivo::find()->orderBy(['descripcion' => SORT_ASC])->all() as $objetivo) {
            $opciones[$objetivo->idobjetivo] = $objetivo->descripcion . ' (' . $objetivo->puntaje . ' pts)';
        }

        return $opciones;
    }

    private function personasSinAlumno(): array
    {
        $personas = Persona::find()
            ->where(['not in', 'idpersona', Alumno::find()->select('idpersona')])
            ->orderBy(['apellido' => SORT_ASC, 'nombre' => SORT_ASC])
            ->all();

        $opciones = [];

        foreach ($personas as $persona) {
            $opciones[$persona->idpersona] = $persona->apellido . ', ' . $persona->nombre . ' (' . $persona->documento . ')';
        }

        return $opciones;
    }

    private function findModel($id): Alumno
    {
        $alumno = Alumno::findOne((int) $id);

        if ($alumno === null) {
            throw new NotFoundHttpException('El alumno no existe.');
        }

        return $alumno;
    }
}
