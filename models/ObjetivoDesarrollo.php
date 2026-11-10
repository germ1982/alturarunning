<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $idobjetivo
 * @property int $idpersona
 * @property int $estado
 *
 * @property Objetivo $objetivo
 */
class ObjetivoDesarrollo extends ActiveRecord
{
    /** Marcado para borrar en el formulario. */
    public bool $eliminar = false;

    public static function tableName()
    {
        return 'persona_objetivo_desarrollo';
    }

    public function rules()
    {
        return [
            [['idobjetivo', 'estado'], 'required'],
            [['idobjetivo'], 'integer'],
            [['idobjetivo'], 'exist', 'targetClass' => Objetivo::class, 'targetAttribute' => 'idobjetivo'],
            [['estado'], 'in', 'range' => array_keys(ConstantesGlobales::ESTADOS_OBJETIVO)],
        ];
    }

    public function attributeLabels()
    {
        return [
            'idobjetivo' => 'Objetivo',
            'estado' => 'Estado',
        ];
    }

    public function getObjetivo()
    {
        return $this->hasOne(Objetivo::class, ['idobjetivo' => 'idobjetivo']);
    }
}
