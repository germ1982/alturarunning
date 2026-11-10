<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $idobjetivo
 * @property string $descripcion
 * @property int $puntaje
 */
class Objetivo extends ActiveRecord
{
    public static function tableName()
    {
        return 'persona_objetivo';
    }

    public function rules()
    {
        return [
            [['descripcion'], 'trim'],
            [['descripcion', 'puntaje'], 'required'],
            [['descripcion'], 'string', 'max' => 255],
            [['puntaje'], 'integer', 'min' => 0],
        ];
    }

    public function attributeLabels()
    {
        return [
            'descripcion' => 'Descripción',
            'puntaje' => 'Puntaje',
        ];
    }
}
