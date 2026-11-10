<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Contenidos editables desde Administrar > Contenidos:
 * - Destacados del home (antes, un video fijo en la vista).
 * - Lugares de entrenamiento (antes, constantes en el código): pasan a
 *   dato_sistema y los horarios existentes se reasignan a los nuevos ids.
 */
class m261110_090000_crear_contenidos_editables extends Migration
{
    private const TIPO_DESTACADO = 8;
    private const TIPO_LUGAR = 9;

    /** Los lugares tal como estaban en ConstantesGlobales::LUGARES (id anterior => nombre). */
    private const LUGARES = [
        1 => 'Parque de los Dinosaurios Neuquen',
        2 => 'Parque Norte Neuquen',
        3 => 'Pista de Atletismo Neuquén',
        4 => 'Estación Transformadora Allen',
        5 => 'Pista de Atletismo Allen',
        6 => 'Letras En Parque Integración de Allen',
    ];

    public function safeUp()
    {
        $this->insert('{{%dato_sistema_tipo}}', ['idtipo' => self::TIPO_DESTACADO, 'tipo' => 'DESTACADO']);
        $this->insert('{{%dato_sistema_tipo}}', ['idtipo' => self::TIPO_LUGAR, 'tipo' => 'LUGAR_ENTRENAMIENTO']);

        // El video que estaba fijo en la tarjeta pasa a ser el primer destacado.
        $this->insert('{{%dato_sistema}}', [
            'idtipo' => self::TIPO_DESTACADO,
            'dato' => 'Villa El Chocón, 28 de julio de 2024',
            'observacion' => 'video:np3_g29e8zs',
        ]);

        $nuevos = [];
        foreach (self::LUGARES as $anterior => $nombre) {
            $this->insert('{{%dato_sistema}}', ['idtipo' => self::TIPO_LUGAR, 'dato' => $nombre]);
            $nuevos[$anterior] = (int) $this->db->getLastInsertID();
        }

        $this->reasignarLugares($nuevos);
    }

    public function safeDown()
    {
        $nuevos = (new Query())
            ->select('iddato')
            ->from('{{%dato_sistema}}')
            ->where(['idtipo' => self::TIPO_LUGAR])
            ->orderBy(['iddato' => SORT_ASC])
            ->column($this->db);

        // Vuelve a los ids originales, en el mismo orden en que se crearon.
        $this->reasignarLugares(array_flip(array_combine(array_keys(self::LUGARES), array_slice(array_pad($nuevos, count(self::LUGARES), 0), 0, count(self::LUGARES)))));

        $this->delete('{{%dato_sistema}}', ['idtipo' => [self::TIPO_DESTACADO, self::TIPO_LUGAR]]);
        $this->delete('{{%dato_sistema_tipo}}', ['idtipo' => [self::TIPO_DESTACADO, self::TIPO_LUGAR]]);
    }

    /**
     * @param array<int, int> $mapa id actual => id nuevo
     */
    private function reasignarLugares(array $mapa): void
    {
        if ($mapa === []) {
            return;
        }

        $casos = '';
        foreach ($mapa as $actual => $nuevo) {
            $casos .= ' WHEN ' . (int) $actual . ' THEN ' . (int) $nuevo;
        }

        $this->execute('UPDATE {{%calendario}} SET idlugar = CASE idlugar' . $casos . ' ELSE idlugar END');
    }
}
