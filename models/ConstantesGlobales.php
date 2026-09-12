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
}
