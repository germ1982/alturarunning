<?php

use yii\db\Migration;

class m260910_192952_crear_tablas_personas extends Migration
{
    public function safeUp()
    {
        // 1. Tabla: Persona
        $this->createTable('{{%persona}}', [
            'idpersona' => $this->primaryKey(),
            'nombre' => $this->string(100)->notNull(),
            'apellido' => $this->string(100)->notNull(),
            'documento' => $this->string(20)->notNull()->unique(),
            'fecha_nacimiento' => $this->date()->null(),
            'direccion' => $this->string(200)->null(),
            'telefono' => $this->string(30)->null(),
            'email' => $this->string(150)->null(),
        ]);

        // 2. Tabla: Alumno (Básica, sin métricas ni nivel por ahora)
        $this->createTable('{{%persona_alumno}}', [
            'idalumno' => $this->primaryKey(),
            'idpersona' => $this->integer()->notNull(),
            'rol' => $this->string(50)->notNull()->defaultValue('corredor'), // caminante o corredor
        ]);

        $this->addForeignKey(
            'fk-persona_alumno-persona',
            '{{%persona_alumno}}',
            'idpersona',
            '{{%persona}}',
            'idpersona',
            'CASCADE',
            'CASCADE'
        );

        // 3. Tabla: Instructor
        $this->createTable('{{%persona_instructor}}', [
            'idinstructor' => $this->primaryKey(),
            'idpersona' => $this->integer()->notNull(),
            'especialidad' => $this->string(100)->null(),
            'biografia' => $this->text()->null(),
        ]);

        $this->addForeignKey(
            'fk-persona_instructor-persona',
            '{{%persona_instructor}}',
            'idpersona',
            '{{%persona}}',
            'idpersona',
            'CASCADE',
            'CASCADE'
        );

        // 4. Tabla: Foto de Persona
        $this->createTable('{{%persona_foto}}', [
            'idfoto' => $this->primaryKey(),
            'idpersona' => $this->integer()->notNull(),
            'ruta' => $this->string(255)->notNull(),
            'es_principal' => $this->boolean()->defaultValue(false),
        ]);

        $this->addForeignKey(
            'fk-persona_foto-persona',
            '{{%persona_foto}}',
            'idpersona',
            '{{%persona}}',
            'idpersona',
            'CASCADE',
            'CASCADE'
        );

        // Guillermo Campos
        $this->insert('{{%persona}}', [
            'nombre' => 'Guillermo',
            'apellido' => 'Campos',
            'documento' => '10000001',
        ]);
        $idPersona = $this->db->getLastInsertID();

        $this->insert('{{%persona_instructor}}', [
            'idpersona' => $idPersona,
            'especialidad' => 'Corredor',
        ]);

        $this->insert('{{%persona_foto}}', [
            'idpersona' => $idPersona,
            'ruta' => '10000001_0000.jpg',
            'es_principal' => true,
        ]);

        // Mario Solano
        $this->insert('{{%persona}}', [
            'nombre' => 'Mario',
            'apellido' => 'Solano',
            'documento' => '10000002',
        ]);
        $idPersona = $this->db->getLastInsertID();

        $this->insert('{{%persona_instructor}}', [
            'idpersona' => $idPersona,
            'especialidad' => 'Corredor',
        ]);

        $this->insert('{{%persona_foto}}', [
            'idpersona' => $idPersona,
            'ruta' => '10000002_0000.jpg',
            'es_principal' => true,
        ]);

        // Diego Hernández
        $this->insert('{{%persona}}', [
            'nombre' => 'Diego',
            'apellido' => 'Hernández',
            'documento' => '10000003',
        ]);
        $idPersona = $this->db->getLastInsertID();

        $this->insert('{{%persona_instructor}}', [
            'idpersona' => $idPersona,
            'especialidad' => 'Trekking',
        ]);

        $this->insert('{{%persona_foto}}', [
            'idpersona' => $idPersona,
            'ruta' => '10000003_0000.jpg',
            'es_principal' => true,
        ]);
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-persona_foto-persona', '{{%persona_foto}}');
        $this->dropForeignKey('fk-persona_instructor-persona', '{{%persona_instructor}}');
        $this->dropForeignKey('fk-persona_alumno-persona', '{{%persona_alumno}}');

        $this->dropTable('{{%persona_foto}}');
        $this->dropTable('{{%persona_instructor}}');
        $this->dropTable('{{%persona_alumno}}');
        $this->dropTable('{{%persona}}');
    }
}
