<?php

namespace app\helpers;

use Yii;

/**
 * Optimiza las fotos al guardarlas en el servidor:
 * - las endereza según la orientación que guarda el celular (EXIF),
 * - las achica si superan el ancho máximo,
 * - las vuelve a comprimir, lo que además borra los datos internos (EXIF),
 *   que en fotos de celular suelen incluir la ubicación GPS.
 *
 * Complementa la compresión que ya hace el navegador (web/js/comprimir-imagenes.js):
 * si esa no corrió, esta igual deja la foto liviana.
 */
class ImagenHelper
{
    /** Anchos máximos por uso. */
    public const ANCHO_BANNER = 1920;
    public const ANCHO_GENERAL = 1280;
    public const ANCHO_PERSONA = 600;
    public const ANCHO_DOCUMENTO = 2000;

    public const CALIDAD_GENERAL = 80;
    public const CALIDAD_DOCUMENTO = 88;

    /**
     * Optimiza en el lugar la imagen de $ruta. Si no puede leerla, la deja como está.
     */
    public static function optimizar(string $ruta, int $anchoMaximo, int $calidad = self::CALIDAD_GENERAL): void
    {
        $info = @getimagesize($ruta);

        if ($info === false || !in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
            return;
        }

        try {
            $imagen = match ($info[2]) {
                IMAGETYPE_JPEG => @imagecreatefromjpeg($ruta),
                IMAGETYPE_PNG => @imagecreatefrompng($ruta),
                IMAGETYPE_WEBP => @imagecreatefromwebp($ruta),
            };

            if ($imagen === false) {
                return;
            }

            if ($info[2] === IMAGETYPE_JPEG) {
                $imagen = self::enderezar($imagen, $ruta);
            }

            $ancho = imagesx($imagen);

            if ($ancho > $anchoMaximo) {
                $imagen = self::achicar($imagen, $anchoMaximo);
            }

            $temporal = $ruta . '.tmp';

            $guardada = match ($info[2]) {
                IMAGETYPE_JPEG => imagejpeg($imagen, $temporal, $calidad),
                IMAGETYPE_WEBP => imagewebp($imagen, $temporal, $calidad),
                IMAGETYPE_PNG => self::guardarPng($imagen, $temporal),
            };

            imagedestroy($imagen);

            if ($guardada && is_file($temporal)) {
                rename($temporal, $ruta);
            } else {
                @unlink($temporal);
            }
        } catch (\Throwable $e) {
            // Una foto que no se pudo optimizar se guarda igual: no debe frenar el formulario.
            Yii::warning('No se pudo optimizar ' . basename($ruta) . ': ' . $e->getMessage());
            @unlink($ruta . '.tmp');
        }
    }

    /** Aplica la rotación que el celular dejó anotada en el EXIF. */
    private static function enderezar(\GdImage $imagen, string $ruta): \GdImage
    {
        if (!function_exists('exif_read_data')) {
            return $imagen;
        }

        $orientacion = (int) (@exif_read_data($ruta)['Orientation'] ?? 1);
        $angulo = [3 => 180, 6 => -90, 8 => 90][$orientacion] ?? 0;

        if ($angulo === 0) {
            return $imagen;
        }

        $rotada = imagerotate($imagen, $angulo, 0);

        if ($rotada === false) {
            return $imagen;
        }

        imagedestroy($imagen);

        return $rotada;
    }

    /** Achica respetando la transparencia (imagescale la pierde en los PNG). */
    private static function achicar(\GdImage $imagen, int $anchoMaximo): \GdImage
    {
        $ancho = imagesx($imagen);
        $alto = imagesy($imagen);
        $nuevoAlto = max(1, (int) round($alto * $anchoMaximo / $ancho));
        $achicada = imagecreatetruecolor($anchoMaximo, $nuevoAlto);

        imagealphablending($achicada, false);
        imagesavealpha($achicada, true);
        imagefill($achicada, 0, 0, imagecolorallocatealpha($achicada, 0, 0, 0, 127));
        imagecopyresampled($achicada, $imagen, 0, 0, 0, 0, $anchoMaximo, $nuevoAlto, $ancho, $alto);
        imagedestroy($imagen);

        return $achicada;
    }

    private static function guardarPng(\GdImage $imagen, string $ruta): bool
    {
        imagealphablending($imagen, false);
        imagesavealpha($imagen, true); // conserva la transparencia

        return imagepng($imagen, $ruta, 6);
    }
}
