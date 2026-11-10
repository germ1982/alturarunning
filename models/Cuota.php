<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $idcuota
 * @property string $mes AAAA-MM
 * @property int $tipo
 * @property string $monto
 *
 * @property CuotaPago[] $pagos
 */
class Cuota extends ActiveRecord
{
    public static function tableName()
    {
        return 'cuotas';
    }

    public function rules()
    {
        return [
            [['mes', 'tipo', 'monto'], 'required'],
            [['mes'], 'match', 'pattern' => '/^\d{4}-(0[1-9]|1[0-2])$/', 'message' => 'El mes debe tener el formato AAAA-MM.'],
            [['tipo'], 'in', 'range' => array_keys(ConstantesGlobales::TIPOS_CUOTA)],
            [['monto'], 'number', 'min' => 0, 'max' => 99999999],
            [['mes'], 'unique', 'targetAttribute' => ['mes', 'tipo'], 'message' => 'Ya existe una cuota de ese tipo para ese mes.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'mes' => 'Mes',
            'tipo' => 'Tipo',
            'monto' => 'Monto',
        ];
    }

    public function getPagos()
    {
        return $this->hasMany(CuotaPago::class, ['idcuota' => 'idcuota']);
    }

    public function getMesTexto(): string
    {
        [$anio, $mes] = explode('-', $this->mes);

        return ConstantesGlobales::MESES[(int) $mes] . ' ' . $anio;
    }

    public function getTipoTexto(): string
    {
        return ConstantesGlobales::TIPOS_CUOTA[$this->tipo] ?? '';
    }

    public function getEtiqueta(): string
    {
        return $this->getMesTexto() . ' · ' . $this->getTipoTexto() . ' (' . EventoDesafio::formatearPrecio((float) $this->monto) . ')';
    }
}
