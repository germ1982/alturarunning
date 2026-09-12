<?php

use yii\db\Migration;

class m260911_021135_crear_tablas_evento extends Migration
{
    public function safeUp()
    {
        // 1. Tabla: Evento
        $this->createTable('{{%evento}}', [
            'idevento' => $this->primaryKey(),
            'titulo' => $this->string(150)->notNull(),
            'descripcion' => $this->text()->null(),
            'fecha_evento' => $this->date()->notNull(),
            'link' => $this->string(255)->null(),
            'contacto' => $this->string(255)->null(),
            'cupo' => $this->integer()->null(),
            'estado' => $this->integer()->notNull(), // Controlado por constantes
        ]);

        // 2. Tabla: Evento Fotos
        $this->createTable('{{%evento_fotos}}', [
            'idfoto' => $this->primaryKey(),
            'idevento' => $this->integer()->notNull(),
            'ruta' => $this->string(255)->notNull(),
            'es_principal' => $this->boolean()->defaultValue(false),
        ]);

        // FK: evento_fotos -> evento
        $this->addForeignKey(
            'fk-evento_fotos-evento',
            '{{%evento_fotos}}',
            'idevento',
            '{{%evento}}',
            'idevento',
            'CASCADE',
            'CASCADE'
        );

        // 3. Tabla: Evento Inscripción
        $this->createTable('{{%evento_inscripcion}}', [
            'idinscripcion' => $this->primaryKey(),
            'idevento' => $this->integer()->notNull(),
            'idpersona' => $this->integer()->notNull(), // Alumno
            'pagado' => $this->integer()->notNull()->defaultValue(0), // 0: No, 1: Sí
            'monto' => $this->decimal(10, 2)->notNull()->defaultValue(0.00),
            'estado' => $this->integer()->notNull(), // Vigente, cancelada, ausente, etc.
        ]);

        // FK: evento_inscripcion -> evento
        $this->addForeignKey(
            'fk-evento_inscripcion-evento',
            '{{%evento_inscripcion}}',
            'idevento',
            '{{%evento}}',
            'idevento',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-evento_inscripcion-evento', '{{%evento_inscripcion}}');
        $this->dropTable('{{%evento_inscripcion}}');

        $this->dropForeignKey('fk-evento_fotos-evento', '{{%evento_fotos}}');
        $this->dropTable('{{%evento_fotos}}');
        
        $this->dropTable('{{%evento}}');
    }
}