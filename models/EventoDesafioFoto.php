<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $idfoto
 * @property int $iddesafio
 * @property string $foto
 * @property int $orden
 * @property int $estado
 */
class EventoDesafioFoto extends ActiveRecord
{
    /** Marcada para borrar en el formulario. */
    public bool $eliminar = false;

    public static function tableName()
    {
        return 'evento_desafio_foto';
    }

    public function rules()
    {
        return [
            [['orden'], 'integer', 'min' => 0],
            [['estado'], 'in', 'range' => array_keys(ConstantesGlobales::ESTADOS_FOTO)],
        ];
    }
}
