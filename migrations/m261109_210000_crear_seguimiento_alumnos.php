<?php

use yii\db\Migration;

/**
 * Seguimiento de alumnos: rol y estado, cuotas con sus pagos y objetivos.
 */
class m261109_210000_crear_seguimiento_alumnos extends Migration
{
    public function safeUp()
    {
        // El rol era texto libre: se traduce a los valores de ConstantesGlobales::ROLES_ALUMNO.
        $this->execute("UPDATE {{%persona_alumno}} SET rol = CASE LOWER(rol) WHEN 'caminante' THEN '1' WHEN 'mixto' THEN '3' ELSE '2' END");
        $this->alterColumn('{{%persona_alumno}}', 'rol', $this->integer()->notNull()->defaultValue(2));
        $this->addColumn('{{%persona_alumno}}', 'estado', $this->integer()->notNull()->defaultValue(1)); // ConstantesGlobales::ESTADOS_ALUMNO
        $this->createIndex('uk_persona_alumno_persona', '{{%persona_alumno}}', 'idpersona', true);

        $this->createTable('{{%cuotas}}', [
            'idcuota' => $this->primaryKey(),
            'mes' => $this->string(7)->notNull(), // AAAA-MM
            'tipo' => $this->integer()->notNull(), // ConstantesGlobales::TIPOS_CUOTA
            'monto' => $this->decimal(10, 2)->notNull(),
        ]);
        $this->createIndex('uk_cuotas_mes_tipo', '{{%cuotas}}', ['mes', 'tipo'], true);

        $this->createTable('{{%cuota_pagos}}', [
            'idpago' => $this->primaryKey(),
            'idcuota' => $this->integer()->notNull(),
            'idalumno' => $this->integer()->notNull(),
            'monto' => $this->decimal(10, 2)->notNull(),
            'estado_pago' => $this->integer()->notNull(), // ConstantesGlobales::ESTADOS_CUOTA_PAGO
            'observacion' => $this->text()->null(),
        ]);
        $this->createIndex('uk_cuota_pagos_cuota_alumno', '{{%cuota_pagos}}', ['idcuota', 'idalumno'], true);
        $this->addForeignKey('fk-cuota_pagos-cuota', '{{%cuota_pagos}}', 'idcuota', '{{%cuotas}}', 'idcuota', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk-cuota_pagos-alumno', '{{%cuota_pagos}}', 'idalumno', '{{%persona_alumno}}', 'idalumno', 'RESTRICT', 'CASCADE');

        $this->createTable('{{%persona_objetivo}}', [
            'idobjetivo' => $this->primaryKey(),
            'descripcion' => $this->string(255)->notNull(),
            'puntaje' => $this->integer()->notNull()->defaultValue(0),
        ]);

        $this->createTable('{{%persona_objetivo_desarrollo}}', [
            'idobjetivo' => $this->integer()->notNull(),
            'idpersona' => $this->integer()->notNull(),
            'estado' => $this->integer()->notNull(), // ConstantesGlobales::ESTADOS_OBJETIVO
            'PRIMARY KEY(idobjetivo, idpersona)',
        ]);
        $this->addForeignKey('fk-objetivo_desarrollo-objetivo', '{{%persona_objetivo_desarrollo}}', 'idobjetivo', '{{%persona_objetivo}}', 'idobjetivo', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk-objetivo_desarrollo-persona', '{{%persona_objetivo_desarrollo}}', 'idpersona', '{{%persona}}', 'idpersona', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-objetivo_desarrollo-persona', '{{%persona_objetivo_desarrollo}}');
        $this->dropForeignKey('fk-objetivo_desarrollo-objetivo', '{{%persona_objetivo_desarrollo}}');
        $this->dropTable('{{%persona_objetivo_desarrollo}}');
        $this->dropTable('{{%persona_objetivo}}');

        $this->dropForeignKey('fk-cuota_pagos-alumno', '{{%cuota_pagos}}');
        $this->dropForeignKey('fk-cuota_pagos-cuota', '{{%cuota_pagos}}');
        $this->dropTable('{{%cuota_pagos}}');
        $this->dropTable('{{%cuotas}}');

        $this->dropIndex('uk_persona_alumno_persona', '{{%persona_alumno}}');
        $this->dropColumn('{{%persona_alumno}}', 'estado');
        $this->alterColumn('{{%persona_alumno}}', 'rol', $this->string(50)->notNull()->defaultValue('corredor'));
        $this->execute("UPDATE {{%persona_alumno}} SET rol = CASE rol WHEN '1' THEN 'caminante' WHEN '3' THEN 'mixto' ELSE 'corredor' END");
    }
}
