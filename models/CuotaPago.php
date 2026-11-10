<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $idpago
 * @property int $idcuota
 * @property int $idalumno
 * @property string $monto
 * @property int $estado_pago
 * @property string|null $observacion
 *
 * @property Cuota $cuota
 * @property Alumno $alumno
 */
class CuotaPago extends ActiveRecord
{
    /** Marcado para borrar en el formulario. */
    public bool $eliminar = false;

    public static function tableName()
    {
        return 'cuota_pagos';
    }

    public function rules()
    {
        return [
            [['observacion'], 'trim'],
            [['observacion'], 'default', 'value' => null],
            [['idcuota', 'monto', 'estado_pago'], 'required'],
            [['idcuota'], 'integer'],
            [['idcuota'], 'exist', 'targetClass' => Cuota::class, 'targetAttribute' => 'idcuota'],
            [['monto'], 'number', 'min' => 0, 'max' => 99999999],
            [['estado_pago'], 'in', 'range' => array_keys(ConstantesGlobales::ESTADOS_CUOTA_PAGO)],
            [['observacion'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'idcuota' => 'Cuota',
            'monto' => 'Monto',
            'estado_pago' => 'Estado',
            'observacion' => 'Observación',
        ];
    }

    public function getCuota()
    {
        return $this->hasOne(Cuota::class, ['idcuota' => 'idcuota']);
    }

    public function getAlumno()
    {
        return $this->hasOne(Alumno::class, ['idalumno' => 'idalumno']);
    }
}
