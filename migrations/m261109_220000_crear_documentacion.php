<?php

use yii\db\Migration;

/**
 * Documentación de personas (apto médico, DNI...). Los archivos se guardan en
 * @app/archivos/documentacion, fuera de la carpeta pública.
 */
class m261109_220000_crear_documentacion extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%documentacion_tipo}}', [
            'idtipo_documentacion' => $this->primaryKey(),
            'descripcion' => $this->string(100)->notNull()->unique(),
        ]);

        $this->batchInsert('{{%documentacion_tipo}}', ['descripcion'], [
            ['Apto médico'],
            ['DNI'],
        ]);

        $this->createTable('{{%persona_documentacion}}', [
            'iddocumentacion' => $this->primaryKey(),
            'idpersona' => $this->integer()->notNull(),
            'idtipo' => $this->integer()->notNull(),
            'archivo' => $this->string(255)->notNull(),
            'observacion' => $this->text()->null(),
        ]);
        $this->addForeignKey('fk-persona_documentacion-persona', '{{%persona_documentacion}}', 'idpersona', '{{%persona}}', 'idpersona', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk-persona_documentacion-tipo', '{{%persona_documentacion}}', 'idtipo', '{{%documentacion_tipo}}', 'idtipo_documentacion', 'RESTRICT', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-persona_documentacion-tipo', '{{%persona_documentacion}}');
        $this->dropForeignKey('fk-persona_documentacion-persona', '{{%persona_documentacion}}');
        $this->dropTable('{{%persona_documentacion}}');
        $this->dropTable('{{%documentacion_tipo}}');
    }
}
