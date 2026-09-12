<?php

use yii\db\Migration;

class m260910_185927_crear_tablas_iniciales extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 1. Tabla: Tipo de Dato del Sistema
        $this->createTable('{{%dato_sistema_tipo}}', [
            'idtipo' => $this->primaryKey(),
            'tipo' => $this->string(100)->notNull()->unique(),
        ]);

        // 2. Tabla: Dato del Sistema
        $this->createTable('{{%dato_sistema}}', [
            'iddato' => $this->primaryKey(),
            'idtipo' => $this->integer()->notNull(),
            'dato' => $this->text()->notNull(),
            'observacion' => $this->text()->null(),
        ]);

        $this->addForeignKey(
            'fk-dato_sistema-tipo_dato_id',
            '{{%dato_sistema}}',
            'idtipo',
            '{{%dato_sistema_tipo}}',
            'idtipo',
            'CASCADE',
            'CASCADE'
        );

        // 3. Tabla: Calendario (Entrenamientos)
        $this->createTable('{{%calendario}}', [
            'id' => $this->primaryKey(),
            'idlugar' => $this->integer()->notNull(),
            'idmodalidad' => $this->integer()->notNull(),
            'idinstructor' => $this->integer()->notNull(),
            'dia' => $this->integer()->notNull(),
            'horario' => $this->string(20)->notNull(),
        ]);

        $this->insert('{{%dato_sistema_tipo}}', [
            'tipo' => 'TIPO_IMAGEN_BANNER',
        ]);

        $this->insert('{{%dato_sistema_tipo}}', [
            'tipo' => 'TIPO_ESPECIALIDAD_INSTRUCTOR',
        ]);

        $this->insert('{{%dato_sistema_tipo}}', [
            'tipo' => 'TIPO_TIPO_CLASE_ENTRENAMIENTO',
        ]);

        $this->batchInsert('{{%dato_sistema}}', ['idtipo', 'dato'], [
            [2, 'Caminantes'],
            [2, 'Corredores'],
            [2, 'Trail'],
            [2, 'Trekking'],
            [2, 'Completo'],
            [3, 'Caminantes'],
            [3, 'Corredores'],
            [3, 'Kids'],
            [3, 'Especial'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-dato_sistema-tipo_dato_id', '{{%dato_sistema}}');
        $this->dropTable('{{%dato_sistema}}');
        $this->dropTable('{{%dato_sistema_tipo}}');
        $this->dropTable('{{%calendario}}');
    }
}
