<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Datos configurables del sitio (banner, frases, about, lugares, destacados...).
 * El tipo indica qué es cada fila: ver ConstantesGlobales::TIPO_*.
 *
 * @property int $iddato
 * @property int $idtipo
 * @property string $dato
 * @property string|null $observacion
 */
class DatoSistema extends ActiveRecord
{
    public static function tableName()
    {
        return 'dato_sistema';
    }

    public function rules()
    {
        return [
            [['dato', 'observacion'], 'trim'],
            [['observacion'], 'default', 'value' => null],
            [['dato'], 'required'],
            [['dato'], 'string', 'max' => 2000],
            [['observacion'], 'string', 'max' => 20000],
        ];
    }

    /**
     * @return self[]
     */
    public static function deTipo(int $tipo): array
    {
        return self::find()->where(['idtipo' => $tipo])->orderBy(['iddato' => SORT_ASC])->all();
    }

    /**
     * @return array<int, string> iddato => dato
     */
    public static function opciones(int $tipo): array
    {
        return self::find()
            ->select(['dato', 'iddato'])
            ->where(['idtipo' => $tipo])
            ->orderBy(['iddato' => SORT_ASC])
            ->indexBy('iddato')
            ->column();
    }

    /** Para un destacado: el id del video de YouTube, si es un video. */
    public function getVideoYoutube(): ?string
    {
        return str_starts_with((string) $this->observacion, 'video:') ? substr($this->observacion, 6) : null;
    }

    /** Para un destacado: el nombre del archivo, si es una foto. */
    public function getImagenDestacado(): ?string
    {
        return str_starts_with((string) $this->observacion, 'imagen:') ? substr($this->observacion, 7) : null;
    }

    /**
     * Saca el id de un enlace de YouTube (watch, youtu.be, embed o shorts), o null si no es válido.
     */
    public static function idYoutube(string $enlace): ?string
    {
        $enlace = trim($enlace);

        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $enlace)) {
            return $enlace;
        }

        if (preg_match('#(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/)|youtu\.be/)([A-Za-z0-9_-]{11})#', $enlace, $coincidencia)) {
            return $coincidencia[1];
        }

        return null;
    }
}
