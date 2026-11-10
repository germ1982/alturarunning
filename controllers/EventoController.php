<?php

namespace app\controllers;

use app\helpers\UserHelper;
use app\models\Alumno;
use app\models\ConstantesGlobales;
use app\models\Evento;
use app\models\EventoDesafio;
use app\models\EventoForm;
use app\models\EventoInscripcion;
use app\models\EventoSearch;
use app\models\Persona;
use app\models\SistemaLog;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class EventoController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'create', 'update', 'delete', 'aprobar', 'rechazar', 'solicitar', 'retirar'],
                'rules' => [
                    [
                        'actions' => ['solicitar', 'retirar'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'aprobar', 'rechazar'],
                        'allow' => true,
                        'matchCallback' => static fn () => UserHelper::puede('eventos'),
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'aprobar' => ['POST'],
                    'rechazar' => ['POST'],
                    'solicitar' => ['POST'],
                    'retirar' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Todos los eventos en tarjetas: primero los próximos y después los ya realizados.
     */
    public function actionListado()
    {
        $puedeGestionar = UserHelper::puede('eventos');
        $query = Evento::find()->with(['fotos', 'desafios.inscripciones.persona']);

        if (!$puedeGestionar) {
            $query->where(['estado' => ConstantesGlobales::EVENTO_ACTIVO]);
        }

        $eventos = $query->all();

        usort($eventos, static function (Evento $a, Evento $b) {
            if ($a->esPasado() !== $b->esPasado()) {
                return $a->esPasado() ? 1 : -1;
            }

            $orden = strcmp($a->fecha_evento, $b->fecha_evento);

            return $a->esPasado() ? -$orden : $orden;
        });

        return $this->render('listado', [
            'eventos' => $eventos,
            'puedeGestionar' => $puedeGestionar,
        ]);
    }

    public function actionVer($id)
    {
        $evento = $this->findModel($id);
        $puedeGestionar = UserHelper::puede('eventos');

        if (!$puedeGestionar && $evento->estado != ConstantesGlobales::EVENTO_ACTIVO) {
            throw new NotFoundHttpException('El evento no existe.');
        }

        $query = EventoInscripcion::find()
            ->joinWith(['desafio', 'persona'])
            ->with('persona.fotoPrincipal')
            ->where(['evento_desafio.idevento' => $evento->idevento])
            ->orderBy(['persona.apellido' => SORT_ASC, 'persona.nombre' => SORT_ASC, 'evento_desafio.iddesafio' => SORT_ASC]);

        if (!$puedeGestionar) {
            $query->andWhere(['estado_inscripcion' => ConstantesGlobales::INSCRIPCIONES_CONFIRMADAS]);
        }

        $inscripciones = $query->all();
        $alumno = Alumno::delUsuarioActual();

        return $this->render('ver', [
            'evento' => $evento,
            'inscriptos' => ArrayHelper::index($inscripciones, null, 'idpersona'),
            'solicitudes' => array_filter($inscripciones, static fn ($i) => (int) $i->estado_inscripcion === ConstantesGlobales::INSCRIPCION_SOLICITADA),
            'puedeGestionar' => $puedeGestionar,
            'misInscripciones' => $alumno === null ? [] : EventoInscripcion::find()
                ->joinWith('desafio')
                ->where(['evento_desafio.idevento' => $evento->idevento, 'evento_inscripcion.idpersona' => $alumno->idpersona])
                ->indexBy('iddesafio')
                ->all(),
            'motivoSinSolicitud' => $this->motivoSinSolicitud($evento, $alumno),
            'lugaresDisponibles' => $evento->cupo === null
                ? null
                : max(0, (int) $evento->cupo - $this->lugaresOcupados($evento->idevento, ConstantesGlobales::INSCRIPCIONES_CON_LUGAR)),
        ]);
    }

    /**
     * Un alumno activo pide inscribirse a un desafío; queda "Solicitada" hasta que la aprueben.
     */
    public function actionSolicitar($iddesafio)
    {
        $desafio = EventoDesafio::findOne((int) $iddesafio);

        if ($desafio === null) {
            throw new NotFoundHttpException('El desafío no existe.');
        }

        $alumno = Alumno::delUsuarioActual();
        $transaccion = Yii::$app->db->beginTransaction();

        try {
            // Mientras dure la transacción, nadie más puede tomar lugar en este evento.
            $evento = $this->bloquearEvento($desafio->idevento);
            $motivo = $this->motivoSinSolicitud($evento, $alumno);
            $existente = $alumno === null ? null : EventoInscripcion::findOne(['iddesafio' => $desafio->iddesafio, 'idpersona' => $alumno->idpersona]);

            if ($motivo !== null) {
                Yii::$app->session->setFlash('error', $motivo);
            } elseif ($existente !== null) {
                Yii::$app->session->setFlash('error', (int) $existente->estado_inscripcion === ConstantesGlobales::INSCRIPCION_CANCELADA
                    ? 'Tu inscripción a este desafío fue cancelada. Consultá con tu profe.'
                    : 'Ya tenés una inscripción en este desafío.');
            } else {
                $inscripcion = new EventoInscripcion([
                    'iddesafio' => $desafio->iddesafio,
                    'idpersona' => $alumno->idpersona,
                    'estado_pago' => ConstantesGlobales::PAGO_PENDIENTE,
                    'estado_inscripcion' => ConstantesGlobales::INSCRIPCION_SOLICITADA,
                    'descuento' => 0,
                ]);
                $inscripcion->save(false);

                SistemaLog::registrar(SistemaLog::MODULO_EVENTOS, SistemaLog::ACCION_SOLICITUD, $inscripcion->idinscripcion, $alumno->persona->getNombreCompleto() . ' solicitó inscribirse en ' . $evento->titulo . ' - ' . $desafio->titulo);
                Yii::$app->session->setFlash('success', 'Tu solicitud se envió y tu lugar quedó reservado. Un profe la va a revisar.');
            }

            $transaccion->commit();
        } catch (\Throwable $e) {
            $transaccion->rollBack();
            throw $e;
        }

        return $this->redirect(['ver', 'id' => $desafio->idevento]);
    }

    /**
     * El alumno retira una solicitud que todavía nadie aprobó.
     */
    public function actionRetirar($id)
    {
        $inscripcion = EventoInscripcion::findOne((int) $id);
        $alumno = Alumno::delUsuarioActual();

        if ($inscripcion === null || $alumno === null || (int) $inscripcion->idpersona !== (int) $alumno->idpersona) {
            throw new NotFoundHttpException('La solicitud no existe.');
        }

        $idevento = $inscripcion->desafio->idevento;

        if ((int) $inscripcion->estado_inscripcion !== ConstantesGlobales::INSCRIPCION_SOLICITADA) {
            Yii::$app->session->setFlash('error', 'Tu inscripción ya fue revisada: para darte de baja consultá con tu profe.');
        } else {
            $inscripcion->delete();
            SistemaLog::registrar(SistemaLog::MODULO_EVENTOS, SistemaLog::ACCION_DELETE, $inscripcion->idinscripcion, $alumno->persona->getNombreCompleto() . ' retiró su solicitud en ' . $inscripcion->desafio->evento->titulo . ' - ' . $inscripcion->desafio->titulo);
            Yii::$app->session->setFlash('success', 'Retiraste tu solicitud.');
        }

        return $this->redirect(['ver', 'id' => $idevento]);
    }

    public function actionAprobar($id)
    {
        return $this->resolverSolicitud($id, ConstantesGlobales::INSCRIPCION_ACTIVA, 'aprobó', SistemaLog::ACCION_APROBAR);
    }

    public function actionRechazar($id)
    {
        return $this->resolverSolicitud($id, ConstantesGlobales::INSCRIPCION_CANCELADA, 'rechazó', SistemaLog::ACCION_RECHAZAR);
    }

    private function resolverSolicitud($id, int $estado, string $verbo, int $accionLog)
    {
        $inscripcion = EventoInscripcion::findOne((int) $id);

        if ($inscripcion === null) {
            throw new NotFoundHttpException('La solicitud no existe.');
        }

        $idevento = $inscripcion->desafio->idevento;
        $transaccion = Yii::$app->db->beginTransaction();

        try {
            $evento = $this->bloquearEvento($idevento);
            $inscripcion->refresh();
            $confirmadas = $this->lugaresOcupados($idevento, ConstantesGlobales::INSCRIPCIONES_CONFIRMADAS);

            if ((int) $inscripcion->estado_inscripcion !== ConstantesGlobales::INSCRIPCION_SOLICITADA) {
                Yii::$app->session->setFlash('error', 'Esa solicitud ya fue revisada.');
            } elseif ($estado === ConstantesGlobales::INSCRIPCION_ACTIVA && $evento->cupo !== null && $confirmadas >= (int) $evento->cupo) {
                Yii::$app->session->setFlash('error', 'El cupo ya está completo. Para aprobarla, primero ampliá el cupo del evento.');
            } else {
                $inscripcion->estado_inscripcion = $estado;
                $inscripcion->save(false);

                SistemaLog::registrar(SistemaLog::MODULO_EVENTOS, $accionLog, $inscripcion->idinscripcion, 'Se ' . $verbo . ' la solicitud de ' . $inscripcion->persona->getNombreCompleto() . ' en ' . $evento->titulo . ' - ' . $inscripcion->desafio->titulo);
                Yii::$app->session->setFlash('success', 'Se ' . $verbo . ' la solicitud de ' . $inscripcion->persona->getNombreCompleto() . '.');
            }

            $transaccion->commit();
        } catch (\Throwable $e) {
            $transaccion->rollBack();
            throw $e;
        }

        return $this->redirect(['ver', 'id' => $idevento]);
    }

    /**
     * Toma el evento con un bloqueo de fila (SELECT ... FOR UPDATE): dos pedidos
     * simultáneos para el mismo evento se atienden de a uno, así el cupo no se pasa.
     */
    private function bloquearEvento($idevento): Evento
    {
        $evento = Evento::findBySql('SELECT * FROM {{%evento}} WHERE idevento = :id FOR UPDATE', [':id' => (int) $idevento])->one();

        if ($evento === null) {
            throw new NotFoundHttpException('El evento no existe.');
        }

        return $evento;
    }

    private function lugaresOcupados($idevento, array $estados): int
    {
        return (int) EventoInscripcion::find()
            ->joinWith('desafio')
            ->where(['evento_desafio.idevento' => (int) $idevento, 'estado_inscripcion' => $estados])
            ->count();
    }

    /**
     * Por qué el usuario actual no puede pedir inscripción (null si puede).
     */
    private function motivoSinSolicitud(Evento $evento, ?Alumno $alumno): ?string
    {
        if ($evento->estado != ConstantesGlobales::EVENTO_ACTIVO || $evento->esPasado()) {
            return 'Este evento ya no recibe inscripciones.';
        }

        if ($alumno === null || !$alumno->getEstaActivo()) {
            return 'Solo los alumnos activos pueden solicitar inscripción.';
        }

        // Las solicitudes pendientes también ocupan lugar: quien pide primero, reserva primero.
        if ($evento->cupo !== null && $this->lugaresOcupados($evento->idevento, ConstantesGlobales::INSCRIPCIONES_CON_LUGAR) >= (int) $evento->cupo) {
            return 'El cupo del evento está completo.';
        }

        return null;
    }

    public function actionIndex()
    {
        $searchModel = new EventoSearch();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->search($this->request->queryParams),
        ]);
    }

    public function actionCreate()
    {
        $evento = new Evento(['estado' => ConstantesGlobales::EVENTO_ACTIVO]);

        return $this->formulario($evento, SistemaLog::ACCION_CREATE, 'Se creó el evento ', 'create');
    }

    public function actionUpdate($id)
    {
        return $this->formulario($this->findModel($id), SistemaLog::ACCION_UPDATE, 'Se editó el evento ', 'update');
    }

    public function actionDelete($id)
    {
        $evento = $this->findModel($id);

        foreach ($evento->desafios as $desafio) {
            if ($desafio->inscripciones !== []) {
                Yii::$app->session->setFlash('error', 'No se puede eliminar un evento con inscriptos.');

                return $this->redirect(['index']);
            }
        }

        $archivos = ArrayHelper::getColumn($evento->fotos, 'ruta');

        foreach ($evento->desafios as $desafio) {
            $archivos = array_merge($archivos, ArrayHelper::getColumn($desafio->fotos, 'foto'));
        }

        $evento->delete();
        array_walk($archivos, [EventoForm::class, 'borrarArchivo']);

        SistemaLog::registrar(SistemaLog::MODULO_EVENTOS, SistemaLog::ACCION_DELETE, (int) $id, 'Se eliminó el evento ' . $evento->titulo);
        Yii::$app->session->setFlash('success', 'El evento se eliminó correctamente.');

        return $this->redirect(['index']);
    }

    private function formulario(Evento $evento, int $accion, string $textoLog, string $vista)
    {
        $modelo = new EventoForm($evento);

        if ($this->request->isPost) {
            // Si PHP recortó el POST (max_input_vars), este campo final no llega.
            if ($this->request->post('formulario_completo') !== '1') {
                Yii::$app->session->setFlash('error', 'El formulario era demasiado grande y no se guardó nada. Avisale al administrador del sitio.');
            } else {
                $modelo->cargar($this->request->post());

                if ($modelo->guardar()) {
                    SistemaLog::registrar(SistemaLog::MODULO_EVENTOS, $accion, $evento->idevento, SistemaLog::conDetalle($textoLog . $evento->titulo, $modelo->cambios));
                    Yii::$app->session->setFlash('success', 'El evento se guardó correctamente.');

                    return $this->redirect(['ver', 'id' => $evento->idevento]);
                }

                Yii::$app->session->setFlash('error', 'Revisá los errores marcados. Si elegiste fotos nuevas, volvé a seleccionarlas.');
            }
        }

        return $this->render($vista, [
            'modelo' => $modelo,
            'personas' => $this->opcionesPersonas(),
        ]);
    }

    private function opcionesPersonas(): array
    {
        $opciones = [];

        foreach (Persona::find()->orderBy(['apellido' => SORT_ASC, 'nombre' => SORT_ASC])->all() as $persona) {
            $opciones[$persona->idpersona] = $persona->apellido . ', ' . $persona->nombre . ' (' . $persona->documento . ')';
        }

        return $opciones;
    }

    private function findModel($id): Evento
    {
        $evento = Evento::findOne((int) $id);

        if ($evento === null) {
            throw new NotFoundHttpException('El evento no existe.');
        }

        return $evento;
    }
}
