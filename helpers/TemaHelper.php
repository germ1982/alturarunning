<?php

namespace app\helpers;

use app\models\ConstantesGlobales;
use Yii;
use yii\db\Query;

/**
 * Tema visual del sitio. Los colores de cada tema están en web/css/site.css
 * (sección "Temas"); acá solo se registra cuáles existen y cuál está activo.
 */
class TemaHelper
{
    public const PREDETERMINADO = 'neon';

    /** clave => [nombre, descripción, color de acento] */
    public const TEMAS = [
        'neon' => ['Neón', 'El rojo de siempre de Altura Running.', '#ff3333'],
        'fuego' => ['Fuego', 'Naranja de pista atlética.', '#ff7a1a'],
        'bosque' => ['Bosque', 'Verde trail para las salidas de montaña.', '#8fd14f'],
        'hielo' => ['Hielo', 'Celeste eléctrico, fresco y nocturno.', '#29c4ff'],
        'energia' => ['Energía', 'Amarillo de alta visibilidad.', '#ffd21f'],
    ];

    private const CLAVE_CACHE = 'tema_sitio';

    public static function actual(): string
    {
        try {
            $tema = Yii::$app->cache->getOrSet(self::CLAVE_CACHE, static function () {
                return (new Query())
                    ->select('dato')
                    ->from('dato_sistema')
                    ->where(['idtipo' => ConstantesGlobales::TIPO_TEMA_SITIO])
                    ->scalar();
            }, 3600);
        } catch (\Throwable $e) {
            // Sin la migración del tema todavía aplicada, se usa el predeterminado.
            return self::PREDETERMINADO;
        }

        return isset(self::TEMAS[$tema]) ? $tema : self::PREDETERMINADO;
    }

    public static function guardar(string $tema): void
    {
        $db = Yii::$app->db;
        $existe = (new Query())->from('dato_sistema')->where(['idtipo' => ConstantesGlobales::TIPO_TEMA_SITIO])->exists($db);

        if ($existe) {
            $db->createCommand()->update('dato_sistema', ['dato' => $tema], ['idtipo' => ConstantesGlobales::TIPO_TEMA_SITIO])->execute();
        } else {
            $db->createCommand()->insert('dato_sistema', ['idtipo' => ConstantesGlobales::TIPO_TEMA_SITIO, 'dato' => $tema])->execute();
        }

        Yii::$app->cache->delete(self::CLAVE_CACHE);
    }

    public static function nombre(string $tema): string
    {
        return self::TEMAS[$tema][0] ?? $tema;
    }
}
