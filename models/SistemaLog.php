<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Auditoría: quién hizo qué, sobre qué registro y cuándo.
 *
 * @property int $id
 * @property int|null $idusuario
 * @property int $idmodulo
 * @property int $idaccion
 * @property int|null $id_registro_afectado
 * @property string $descripcion
 * @property string $fecha
 *
 * @property User $usuario
 */
class SistemaLog extends ActiveRecord
{
    // Módulos (los valores se conservan para no romper los registros existentes)
    const MODULO_USUARIOS = 6;
    const MODULO_DATOS = 7;
    const MODULO_EVENTOS = 9;
    const MODULO_SITE = 11;
    const MODULO_ALUMNOS = 12;
    const MODULO_CUOTAS = 13;
    const MODULO_OBJETIVOS = 14;
    const MODULO_DOCUMENTACION = 15;
    const MODULO_ROLES = 16;

    const MODULOS = [
        self::MODULO_USUARIOS => 'Usuarios',
        self::MODULO_ROLES => 'Roles',
        self::MODULO_ALUMNOS => 'Alumnos',
        self::MODULO_CUOTAS => 'Cuotas',
        self::MODULO_OBJETIVOS => 'Objetivos',
        self::MODULO_DOCUMENTACION => 'Documentación',
        self::MODULO_EVENTOS => 'Eventos',
        self::MODULO_DATOS => 'Contenidos',
        self::MODULO_SITE => 'Sitio',
    ];

    // Acciones
    const ACCION_CREATE = 0;
    const ACCION_UPDATE = 1;
    const ACCION_DELETE = 2;
    const ACCION_BANEAR = 3;
    const ACCION_REACTIVAR = 4;
    const ACCION_VER = 5;
    const ACCION_SOLICITUD = 6;
    const ACCION_INICIO_SESION = 7;
    const ACCION_LOGIN_FALLIDO = 8;
    const ACCION_APROBAR = 9;
    const ACCION_RECHAZAR = 10;

    const ACCIONES = [
        self::ACCION_CREATE => 'Crear',
        self::ACCION_UPDATE => 'Actualizar',
        self::ACCION_DELETE => 'Eliminar',
        self::ACCION_BANEAR => 'Banear',
        self::ACCION_REACTIVAR => 'Reactivar',
        self::ACCION_VER => 'Visualización',
        self::ACCION_SOLICITUD => 'Solicitud',
        self::ACCION_INICIO_SESION => 'Inicio de sesión',
        self::ACCION_LOGIN_FALLIDO => 'Inicio de sesión fallido',
        self::ACCION_APROBAR => 'Aprobar',
        self::ACCION_RECHAZAR => 'Rechazar',
    ];

    public static function tableName()
    {
        return '{{%sistema_log}}';
    }

    public function rules()
    {
        return [
            [['idusuario', 'idmodulo', 'idaccion', 'id_registro_afectado'], 'integer'],
            [['idmodulo', 'idaccion', 'descripcion'], 'required'],
            [['descripcion'], 'string'],
            [['fecha'], 'safe'],
            [['idusuario'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['idusuario' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'Nº',
            'idusuario' => 'Usuario',
            'idmodulo' => 'Módulo',
            'idaccion' => 'Acción',
            'id_registro_afectado' => 'Registro afectado',
            'descripcion' => 'Descripción',
            'fecha' => 'Fecha',
        ];
    }

    public function getUsuario()
    {
        return $this->hasOne(User::class, ['id' => 'idusuario']);
    }

    public static function getModuloNombre($id)
    {
        return self::MODULOS[$id] ?? 'Desconocido';
    }

    public static function getAccionNombre($id)
    {
        return self::ACCIONES[$id] ?? 'Desconocida';
    }

    public static function getListaModulos()
    {
        return self::MODULOS;
    }

    public static function getListaAcciones()
    {
        return self::ACCIONES;
    }

    /**
     * Guarda un registro de auditoría a nombre del usuario logueado (o ninguno).
     */
    public static function registrar($idmodulo, $idaccion, $id_registro_afectado = null, $descripcion = ''): bool
    {
        $usuario = Yii::$app->has('user') ? Yii::$app->user : null;

        $log = new self();
        $log->idusuario = $usuario !== null && !$usuario->isGuest ? $usuario->id : null;
        $log->idmodulo = $idmodulo;
        $log->idaccion = $idaccion;
        $log->id_registro_afectado = $id_registro_afectado;
        $log->descripcion = $descripcion;
        $log->fecha = date('Y-m-d H:i:s');

        if ($log->save()) {
            return true;
        }

        Yii::error('Error al registrar log: ' . json_encode($log->errors));

        return false;
    }

    /**
     * Describe un cambio de valor, o null si no cambió.
     *
     * @param array|string|null $mapa valores => textos (por ejemplo una lista de ConstantesGlobales),
     *                                o 'texto' para campos largos: solo avisa que se modificó.
     */
    public static function cambio(string $etiqueta, $antes, $despues, $mapa = null): ?string
    {
        if (self::mismoValor($antes, $despues)) {
            return null;
        }

        if ($mapa === 'texto') {
            return $etiqueta . ' modificada';
        }

        return $etiqueta . ': ' . self::valorTexto($antes, $mapa) . ' → ' . self::valorTexto($despues, $mapa);
    }

    /**
     * Cambios de un ActiveRecord todavía sin guardar.
     *
     * @param array $campos atributo => etiqueta, o atributo => [etiqueta, mapa]
     * @return string[]
     */
    public static function diferencias(ActiveRecord $modelo, array $campos): array
    {
        $cambios = [];

        foreach ($campos as $atributo => $definicion) {
            [$etiqueta, $mapa] = is_array($definicion) ? $definicion + [1 => null] : [$definicion, null];
            $cambio = self::cambio($etiqueta, $modelo->getOldAttribute($atributo), $modelo->getAttribute($atributo), $mapa);

            if ($cambio !== null) {
                $cambios[] = $cambio;
            }
        }

        return $cambios;
    }

    /**
     * Descripción con el detalle de los cambios, uno por línea.
     */
    public static function conDetalle(string $descripcion, array $cambios): string
    {
        return $cambios === [] ? $descripcion : $descripcion . "\n- " . implode("\n- ", $cambios);
    }

    private static function mismoValor($antes, $despues): bool
    {
        $antes = $antes === null ? '' : trim((string) $antes);
        $despues = $despues === null ? '' : trim((string) $despues);

        if (is_numeric($antes) && is_numeric($despues)) {
            return (float) $antes === (float) $despues;
        }

        return $antes === $despues;
    }

    private static function valorTexto($valor, $mapa): string
    {
        if ($valor === null || trim((string) $valor) === '') {
            return '—';
        }

        if (is_array($mapa)) {
            return (string) ($mapa[$valor] ?? $valor);
        }

        $texto = (string) $valor;

        return mb_strlen($texto) > 80 ? mb_substr($texto, 0, 77) . '...' : $texto;
    }
}
