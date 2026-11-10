<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $idfoto
 * @property int $idevento
 * @property string $ruta
 * @property bool $es_principal
 */
class EventoFoto extends ActiveRecord
{
    /** Marcada para borrar en el formulario. */
    public bool $eliminar = false;

    public static function tableName()
    {
        return 'evento_fotos';
    }
}
