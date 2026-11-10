<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $idfoto
 * @property int $idpersona
 * @property string $ruta
 * @property bool $es_principal
 */
class PersonaFoto extends ActiveRecord
{
    public static function tableName()
    {
        return 'persona_foto';
    }
}
