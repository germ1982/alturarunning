<?php

namespace app\models;

use yii\base\Component;

class ConstantesGlobales extends Component
{
    const TIPO_IMAGEN_BANNER = 1;
    const TIPO_ESPECIALIDAD_INSTRUCTOR = 2;
    const TIPO_TIPO_CLASE_ENTRENAMIENTO = 3;
    const TIPO_FRASES_DINO = 4;
    const ABOUT_IMAGEN = 5;
    const ABOUT_PARRAFO = 6;

    const DOMINGO = 1;
    const LUNES = 2;
    const MARTES = 3;
    const MIERCOLES = 4;
    const JUEVES = 5;
    const VIERNES = 6;
    const SABADO = 7;

    const DIAS = [
        self::DOMINGO => 'Domingo',
        self::LUNES => 'Lunes',
        self::MARTES => 'Martes',
        self::MIERCOLES => 'Miércoles',
        self::JUEVES => 'Jueves',
        self::VIERNES => 'Viernes',
        self::SABADO => 'Sábado',
    ];

    const ENERO = 1;
    const FEBRERO = 2;
    const MARZO = 3;
    const ABRIL = 4;
    const MAYO = 5;
    const JUNIO = 6;
    const JULIO = 7;
    const AGOSTO = 8;
    const SEPTIEMBRE = 9;
    const OCTUBRE = 10;
    const NOVIEMBRE = 11;
    const DICIEMBRE = 12;

    const MESES = [
        self::ENERO => 'Enero',
        self::FEBRERO => 'Febrero',
        self::MARZO => 'Marzo',
        self::ABRIL => 'Abril',
        self::MAYO => 'Mayo',
        self::JUNIO => 'Junio',
        self::JULIO => 'Julio',
        self::AGOSTO => 'Agosto',
        self::SEPTIEMBRE => 'Septiembre',
        self::OCTUBRE => 'Octubre',
        self::NOVIEMBRE => 'Noviembre',
        self::DICIEMBRE => 'Diciembre',
    ];

    const PARQUE_DE_LOS_DINOSAURIOS = 1;
    const PARQUE_NORTE = 2;
    const PISTA_ATLETISMO_NEUQUEN = 3;
    const ESTACION_TRANSFORMADORA_ALLEN = 4;
    const PISTA_ATLETISMO_ALLEN = 5;
    const LETRAS_PARQUE_INTEGRACION = 6;


    const LUGARES = [
        self::PARQUE_DE_LOS_DINOSAURIOS => 'Parque de los Dinosaurios Neuquen',
        self::PARQUE_NORTE => 'Parque Norte Neuquen',
        self::PISTA_ATLETISMO_NEUQUEN => 'Pista de Atletismo Neuquén',
        self::ESTACION_TRANSFORMADORA_ALLEN => 'Estación Transformadora Allen',
        self::PISTA_ATLETISMO_ALLEN => 'Pista de Atletismo Allen',
        self::LETRAS_PARQUE_INTEGRACION => 'Letras En Parque Integración de Allen',
    ];

    const EVENTO_INACTIVO = 0;
    const EVENTO_ACTIVO = 1;

    const ESTADOS_EVENTO = [
        self::EVENTO_ACTIVO => 'Activo',
        self::EVENTO_INACTIVO => 'Inactivo',
    ];

    const DIFICULTAD_BAJA = 1;
    const DIFICULTAD_MEDIA = 2;
    const DIFICULTAD_ALTA = 3;

    const DIFICULTADES = [
        self::DIFICULTAD_BAJA => 'Baja',
        self::DIFICULTAD_MEDIA => 'Media',
        self::DIFICULTAD_ALTA => 'Alta',
    ];

    const FOTO_INACTIVA = 0;
    const FOTO_ACTIVA = 1;

    const ESTADOS_FOTO = [
        self::FOTO_ACTIVA => 'Visible',
        self::FOTO_INACTIVA => 'Oculta',
    ];

    const PAGO_COMPLETO = 1;
    const PAGO_PENDIENTE = 2;
    const PAGO_PARCIAL = 3;

    const ESTADOS_PAGO = [
        self::PAGO_COMPLETO => 'Completo',
        self::PAGO_PENDIENTE => 'Pendiente',
        self::PAGO_PARCIAL => 'Parcial',
    ];

    const INSCRIPCION_ACTIVA = 1;
    const INSCRIPCION_CANCELADA = 2;
    const INSCRIPCION_GUARDADA = 3;
    const INSCRIPCION_SOLICITADA = 4; // pedida por el alumno, espera que la apruebe un profe

    const ESTADOS_INSCRIPCION = [
        self::INSCRIPCION_ACTIVA => 'Activa',
        self::INSCRIPCION_CANCELADA => 'Cancelada',
        self::INSCRIPCION_GUARDADA => 'Guardada',
        self::INSCRIPCION_SOLICITADA => 'Solicitada',
    ];

    /** Inscripciones que se muestran al público y ocupan cupo. */
    const INSCRIPCIONES_CONFIRMADAS = [self::INSCRIPCION_ACTIVA, self::INSCRIPCION_GUARDADA];

    /** Inscripciones que reservan lugar: las confirmadas más las solicitudes pendientes. */
    const INSCRIPCIONES_CON_LUGAR = [self::INSCRIPCION_ACTIVA, self::INSCRIPCION_GUARDADA, self::INSCRIPCION_SOLICITADA];

    const ALUMNO_CAMINANTE = 1;
    const ALUMNO_CORREDOR = 2;
    const ALUMNO_MIXTO = 3;

    const ROLES_ALUMNO = [
        self::ALUMNO_CAMINANTE => 'Caminante',
        self::ALUMNO_CORREDOR => 'Corredor',
        self::ALUMNO_MIXTO => 'Mixto',
    ];

    const ALUMNO_ACTIVO = 1;
    const ALUMNO_INACTIVO = 2;
    const ALUMNO_SANCIONADO = 3;

    const ESTADOS_ALUMNO = [
        self::ALUMNO_ACTIVO => 'Activo',
        self::ALUMNO_INACTIVO => 'Inactivo',
        self::ALUMNO_SANCIONADO => 'Sancionado',
    ];

    const CUOTA_CLASE_NORMAL = 1;
    const CUOTA_CLASE_PERSONALIZADA = 2;

    const TIPOS_CUOTA = [
        self::CUOTA_CLASE_NORMAL => 'Clase normal',
        self::CUOTA_CLASE_PERSONALIZADA => 'Clase personalizada',
    ];

    const CUOTA_PENDIENTE = 1;
    const CUOTA_ATRASADO = 2;
    const CUOTA_SALDADO = 3;
    const CUOTA_PACTADO = 4;
    const CUOTA_RECARGO_ATRASO = 5;
    const CUOTA_DEUDOR = 6;

    const ESTADOS_CUOTA_PAGO = [
        self::CUOTA_PENDIENTE => 'Pendiente',
        self::CUOTA_ATRASADO => 'Atrasado',
        self::CUOTA_SALDADO => 'Saldado',
        self::CUOTA_PACTADO => 'Pactado',
        self::CUOTA_RECARGO_ATRASO => 'Recargo por atraso',
        self::CUOTA_DEUDOR => 'Deudor',
    ];

    const OBJETIVO_PENDIENTE = 1;
    const OBJETIVO_EN_CURSO = 2;
    const OBJETIVO_CUMPLIDO = 3;

    const ESTADOS_OBJETIVO = [
        self::OBJETIVO_PENDIENTE => 'Pendiente',
        self::OBJETIVO_EN_CURSO => 'En curso',
        self::OBJETIVO_CUMPLIDO => 'Cumplido',
    ];
}
