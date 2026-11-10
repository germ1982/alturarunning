<?php

namespace app\models;

use app\helpers\ImagenHelper;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\validators\ImageValidator;
use yii\web\UploadedFile;

/**
 * Fotos de personas (alumnos y profesores). Los archivos viven en web/images/personas.
 *
 * @property int $idfoto
 * @property int $idpersona
 * @property string $ruta nombre del archivo
 * @property bool $es_principal
 */
class PersonaFoto extends ActiveRecord
{
    public const CARPETA = 'images/personas';

    public static function tableName()
    {
        return 'persona_foto';
    }

    public static function validar(UploadedFile $archivo, ?string &$error = null): bool
    {
        $validador = new ImageValidator(['extensions' => ['jpg', 'jpeg', 'png', 'webp'], 'maxSize' => 8 * 1024 * 1024]);

        if ($validador->validate($archivo, $mensaje)) {
            return true;
        }

        $error = $archivo->name . ': ' . $mensaje;

        return false;
    }

    /**
     * Cambia o quita la foto principal de una persona. Pensado para usarse dentro de una transacción:
     * deja en $escrito el archivo nuevo (para borrarlo si algo falla) y en $aBorrar los viejos
     * (para borrarlos recién cuando todo se confirmó).
     */
    public static function reemplazarPrincipal(int $idpersona, ?UploadedFile $archivo, bool $quitar, ?string &$escrito, array &$aBorrar): void
    {
        if ($archivo === null && !$quitar) {
            return;
        }

        foreach (self::findAll(['idpersona' => $idpersona, 'es_principal' => 1]) as $anterior) {
            $aBorrar[] = $anterior->ruta;
            $anterior->delete();
        }

        if ($archivo === null) {
            return;
        }

        FileHelper::createDirectory(Yii::getAlias('@webroot/' . self::CARPETA));
        $nombre = Yii::$app->security->generateRandomString(24) . '.' . strtolower($archivo->extension);

        if (!$archivo->saveAs(Yii::getAlias('@webroot/' . self::CARPETA . '/' . $nombre))) {
            throw new \RuntimeException('No se pudo guardar la foto.');
        }

        $escrito = $nombre;
        ImagenHelper::optimizar(Yii::getAlias('@webroot/' . self::CARPETA . '/' . $nombre), ImagenHelper::ANCHO_PERSONA);

        (new self(['idpersona' => $idpersona, 'ruta' => $nombre, 'es_principal' => 1]))->save(false);
    }

    public static function borrarArchivo($nombre): void
    {
        if ((string) $nombre !== '' && basename((string) $nombre) === $nombre) {
            @unlink(Yii::getAlias('@webroot/' . self::CARPETA . '/' . $nombre));
        }
    }
}
