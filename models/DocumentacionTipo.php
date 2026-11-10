<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $idtipo_documentacion
 * @property string $descripcion
 */
class DocumentacionTipo extends ActiveRecord
{
    public static function tableName()
    {
        return 'documentacion_tipo';
    }

    public function rules()
    {
        return [
            [['descripcion'], 'trim'],
            [['descripcion'], 'required'],
            [['descripcion'], 'string', 'max' => 100],
            [['descripcion'], 'unique', 'message' => 'Ese tipo de documentación ya existe.'],
        ];
    }

    public function attributeLabels()
    {
        return ['descripcion' => 'Descripción'];
    }

    public static function opciones(): array
    {
        return self::find()
            ->select(['descripcion', 'idtipo_documentacion'])
            ->orderBy(['descripcion' => SORT_ASC])
            ->indexBy('idtipo_documentacion')
            ->column();
    }
}
