<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sistema_log".
 *
 * @property int $id_log
 * @property int|null $idusuario
 * @property int $idmodulo
 * @property int $idaccion
 * @property int|null $id_registro_afectado
 * @property string $descripcion
 * @property string $fecha
 *
 * @property User $usuario
 */
class SistemaLog extends \yii\db\ActiveRecord
{
    // Constantes de MÓDULOS
    const MODULO_MIEMBROS = 0;
    const MODULO_VENTAS = 1;
    const MODULO_SERVICIOS = 2;
    const MODULO_CONFIG = 3;
    const MODULO_EMPRENDIMIENTOS = 4;
    const MODULO_TRABAJOS = 5; // <--- Agregado aquí
    const MODULO_USUARIOS = 6;
    const MODULO_DATOS = 7;
    const MODULO_GRUPOS = 8;
    const MODULO_EVENTOS = 9;
    const MODULO_INMOBILIARIO = 10;
    const MODULO_SITE = 11;


    // Constantes de ACCIONES
    const ACCION_CREATE = 0;
    const ACCION_UPDATE = 1;
    const ACCION_DELETE = 2;
    const ACCION_BANEAR = 3;
    const ACCION_REACTIVAR = 4;
    const ACCION_VER = 5;
    const ACCION_SOLICITUD = 6;
    const ACCION_INICIO_SESION = 7;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%sistema_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idusuario', 'idmodulo', 'idaccion', 'id_registro_afectado', 'id'], 'integer'],
            [['idmodulo', 'idaccion', 'descripcion'], 'required'],
            [['descripcion'], 'string'],
            [['fecha'], 'safe'],
            [['idusuario'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['idusuario' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Id Log',
            'idusuario' => 'Id Usuario',
            'idmodulo' => 'Id Módulo',
            'idaccion' => 'Id Acción',
            'id_registro_afectado' => 'Id Registro Afectado',
            'descripcion' => 'Descripción',
            'observacion' => 'Observación',
            'ip' => 'Ip',
            'fecha' => 'Fecha',
        ];
    }

    /**
     * Gets query for [[Usuario]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(User::class, ['id' => 'idusuario']);
    }

    /**
     * Retorna el nombre del módulo según su ID.
     *
     * @param int $id El ID del módulo.
     * @return string
     */
    public static function getModuloNombre($id)
    {
        $modulos = [
            self::MODULO_MIEMBROS => 'Miembros',
            self::MODULO_VENTAS => 'Ventas',
            self::MODULO_SERVICIOS => 'Servicios',
            self::MODULO_CONFIG => 'Configuración',
            self::MODULO_EMPRENDIMIENTOS => 'Emprendimientos',
            self::MODULO_TRABAJOS => 'Trabajos',
            self::MODULO_USUARIOS => 'Usuarios',
            self::MODULO_DATOS => 'Datos',
            self::MODULO_GRUPOS => 'Grupos',
            self::MODULO_EVENTOS => 'Eventos',
            self::MODULO_INMOBILIARIO => 'Inmobiliarios',
            self::MODULO_SITE => 'Site'
        ];

        return $modulos[$id] ?? 'Desconocido';
    }

    /**
     * Retorna el nombre de la acción según su ID.
     *
     * @param int $id El ID de la acción.
     * @return string
     */
    public static function getAccionNombre($id)
    {
        $acciones = [
            self::ACCION_CREATE => 'Crear',
            self::ACCION_UPDATE => 'Actualizar',
            self::ACCION_DELETE => 'Eliminar',
            self::ACCION_BANEAR => 'Banear',
            self::ACCION_REACTIVAR => 'Reactivar',
            self::ACCION_VER => 'Visualizacion',
            self::ACCION_SOLICITUD => 'Solicitud',
            self::ACCION_INICIO_SESION => 'Inicio Sesion',
            
        ];
        return $acciones[$id] ?? 'Desconocida';
    }

    public static function registrar($idmodulo, $idaccion, $id_registro_afectado = null, $descripcion = '')
    {
        $idusuario = Yii::$app->user->id ?? null;
        $log = new self();
        $log->idusuario = $idusuario;
        $log->idmodulo = $idmodulo;
        $log->idaccion = $idaccion;
        $log->id_registro_afectado = $id_registro_afectado;
        $log->descripcion = $descripcion;
        $log->fecha = date('Y-m-d H:i:s');
        if ($log->save()) {
            return $log->save();
        } else {
            Yii::error('Error al registrar log: ' . json_encode($log->errors));
            return false;
        }
    }

    // ... dentro de class SistemaLog ...

    /**
     * Retorna el array completo de módulos para filtros.
     */
    public static function getListaModulos()
    {
        return [
            self::MODULO_MIEMBROS => 'Miembros',
            self::MODULO_VENTAS => 'Ventas',
            self::MODULO_SERVICIOS => 'Servicios',
            self::MODULO_CONFIG => 'Configuración',
            self::MODULO_EMPRENDIMIENTOS => 'Emprendimientos',
            self::MODULO_TRABAJOS => 'Trabajos',
            self::MODULO_USUARIOS => 'Usuarios',
            self::MODULO_DATOS => 'Datos',
            self::MODULO_GRUPOS => 'Grupos',
            self::MODULO_EVENTOS => 'Eventos',
            self::MODULO_INMOBILIARIO => 'Inmobiliario',
            self::MODULO_SITE => 'Site',
        ];
    }

    /**
     * Retorna el array completo de acciones para filtros.
     */
    public static function getListaAcciones()
    {
        return [
            self::ACCION_CREATE => 'Crear',
            self::ACCION_UPDATE => 'Actualizar',
            self::ACCION_DELETE => 'Eliminar',
            self::ACCION_BANEAR => 'Banear',
            self::ACCION_REACTIVAR => 'Reactivar',
            self::ACCION_VER => 'Visualizacion',
            self::ACCION_SOLICITUD => 'Solicitud',
            self::ACCION_INICIO_SESION => 'Inicio Sesion',
        ];
    }
}
