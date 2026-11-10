<?php

use yii\db\Migration;

/**
 * Guarda el tema visual del sitio (Administrar > Aspecto) en dato_sistema.
 */
class m261109_230000_crear_tema_sitio extends Migration
{
    private const IDTIPO = 7; // ConstantesGlobales::TIPO_TEMA_SITIO

    public function safeUp()
    {
        $this->insert('{{%dato_sistema_tipo}}', ['idtipo' => self::IDTIPO, 'tipo' => 'TEMA_SITIO']);
        $this->insert('{{%dato_sistema}}', ['idtipo' => self::IDTIPO, 'dato' => 'neon']);
    }

    public function safeDown()
    {
        $this->delete('{{%dato_sistema}}', ['idtipo' => self::IDTIPO]);
        $this->delete('{{%dato_sistema_tipo}}', ['idtipo' => self::IDTIPO]);
    }
}
