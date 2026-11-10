<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $idinscripcion
 * @property int $iddesafio
 * @property int $idpersona
 * @property int $estado_pago
 * @property int $estado_inscripcion
 * @property string $descuento porcentaje
 * @property int|null $puesto
 * @property string|null $tiempo
 * @property string|null $observacion
 *
 * @property EventoDesafio $desafio
 * @property Persona $persona
 */
class EventoInscripcion extends ActiveRecord
{
    /** Marcada para borrar en el formulario. */
    public bool $eliminar = false;

    public static function tableName()
    {
        return 'evento_inscripcion';
    }

    public function rules()
    {
        return [
            [['tiempo', 'observacion'], 'trim'],
            [['puesto', 'tiempo', 'observacion'], 'default', 'value' => null],
            [['descuento'], 'default', 'value' => 0],
            [['idpersona', 'estado_pago', 'estado_inscripcion'], 'required'],
            [['idpersona'], 'integer'],
            [['idpersona'], 'exist', 'targetClass' => Persona::class, 'targetAttribute' => 'idpersona'],
            [['estado_pago'], 'in', 'range' => array_keys(ConstantesGlobales::ESTADOS_PAGO)],
            [['estado_inscripcion'], 'in', 'range' => array_keys(ConstantesGlobales::ESTADOS_INSCRIPCION)],
            [['descuento'], 'number', 'min' => 0, 'max' => 100],
            [['puesto'], 'integer', 'min' => 1],
            [['tiempo'], 'match', 'pattern' => '/^\d{1,3}:[0-5]\d:[0-5]\d$/', 'message' => 'El tiempo debe tener el formato hh:mm:ss.'],
            [['observacion'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'idpersona' => 'Persona',
            'estado_pago' => 'Pago',
            'estado_inscripcion' => 'Estado',
            'descuento' => 'Descuento %',
            'puesto' => 'Puesto',
            'tiempo' => 'Tiempo',
            'observacion' => 'Observación',
        ];
    }

    public function getDesafio()
    {
        return $this->hasOne(EventoDesafio::class, ['iddesafio' => 'iddesafio']);
    }

    public function getPersona()
    {
        return $this->hasOne(Persona::class, ['idpersona' => 'idpersona']);
    }

    public function getMontoFinal(): float
    {
        return (float) $this->desafio->precio * (1 - (float) $this->descuento / 100);
    }
}
