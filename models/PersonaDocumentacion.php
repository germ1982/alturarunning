<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property int $iddocumentacion
 * @property int $idpersona
 * @property int $idtipo
 * @property string $archivo nombre del archivo dentro de la carpeta privada
 * @property string|null $observacion
 *
 * @property DocumentacionTipo $tipo
 */
class PersonaDocumentacion extends ActiveRecord
{
    public const CARPETA = '@app/archivos/documentacion';
    public const EXTENSIONES = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];

    public static function tableName()
    {
        return 'persona_documentacion';
    }

    public function rules()
    {
        return [
            [['observacion'], 'trim'],
            [['observacion'], 'default', 'value' => null],
            [['idtipo'], 'required'],
            [['idtipo'], 'integer'],
            [['idtipo'], 'exist', 'targetClass' => DocumentacionTipo::class, 'targetAttribute' => 'idtipo_documentacion'],
            [['observacion'], 'string', 'max' => 1000],
        ];
    }

    public function attributeLabels()
    {
        return [
            'idtipo' => 'Tipo',
            'observacion' => 'Observación',
        ];
    }

    public function getTipo()
    {
        return $this->hasOne(DocumentacionTipo::class, ['idtipo_documentacion' => 'idtipo']);
    }

    public function getRutaArchivo(): string
    {
        return Yii::getAlias(self::CARPETA) . DIRECTORY_SEPARATOR . basename($this->archivo);
    }
}
