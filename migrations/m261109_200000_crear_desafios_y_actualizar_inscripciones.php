<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Cada evento pasa a tener desafíos (8 km, 15 km, 21 km...) con su precio,
 * y las inscripciones se hacen a un desafío en lugar de al evento.
 */
class m261109_200000_crear_desafios_y_actualizar_inscripciones extends Migration
{
    public function safeUp()
    {
        if ($this->hayInscripciones()) {
            echo "evento_inscripcion tiene datos: hay que asignarles un desafío antes de migrar.\n";
            return false;
        }

        $this->createTable('{{%evento_desafio}}', [
            'iddesafio' => $this->primaryKey(),
            'idevento' => $this->integer()->notNull(),
            'titulo' => $this->string(150)->notNull(),
            'descripcion' => $this->text()->null(),
            'dificultad' => $this->integer()->notNull(), // ConstantesGlobales::DIFICULTADES
            'precio' => $this->decimal(10, 2)->notNull()->defaultValue(0),
        ]);
        $this->addForeignKey('fk-evento_desafio-evento', '{{%evento_desafio}}', 'idevento', '{{%evento}}', 'idevento', 'CASCADE', 'CASCADE');

        $this->createTable('{{%evento_desafio_foto}}', [
            'idfoto' => $this->primaryKey(),
            'iddesafio' => $this->integer()->notNull(),
            'foto' => $this->string(255)->notNull(),
            'orden' => $this->integer()->notNull()->defaultValue(0),
            'estado' => $this->integer()->notNull()->defaultValue(1), // ConstantesGlobales::ESTADOS_FOTO
        ]);
        $this->addForeignKey('fk-evento_desafio_foto-desafio', '{{%evento_desafio_foto}}', 'iddesafio', '{{%evento_desafio}}', 'iddesafio', 'CASCADE', 'CASCADE');

        $tabla = '{{%evento_inscripcion}}';
        $this->dropForeignKey('fk-evento_inscripcion-evento', $tabla);
        $this->dropColumn($tabla, 'idevento');
        $this->dropColumn($tabla, 'pagado');
        $this->dropColumn($tabla, 'monto');
        $this->dropColumn($tabla, 'estado');

        $this->addColumn($tabla, 'iddesafio', $this->integer()->notNull()->after('idinscripcion'));
        $this->addColumn($tabla, 'estado_pago', $this->integer()->notNull()->after('idpersona')); // ConstantesGlobales::ESTADOS_PAGO
        $this->addColumn($tabla, 'estado_inscripcion', $this->integer()->notNull()->after('estado_pago')); // ConstantesGlobales::ESTADOS_INSCRIPCION
        $this->addColumn($tabla, 'descuento', $this->decimal(5, 2)->notNull()->defaultValue(0)->after('estado_inscripcion')); // porcentaje
        $this->addColumn($tabla, 'puesto', $this->integer()->null()->after('descuento'));
        $this->addColumn($tabla, 'tiempo', $this->time()->null()->after('puesto'));
        $this->addColumn($tabla, 'observacion', $this->text()->null()->after('tiempo'));

        $this->createIndex('uk_inscripcion_desafio_persona', $tabla, ['iddesafio', 'idpersona'], true);
        $this->addForeignKey('fk-evento_inscripcion-desafio', $tabla, 'iddesafio', '{{%evento_desafio}}', 'iddesafio', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk-evento_inscripcion-persona', $tabla, 'idpersona', '{{%persona}}', 'idpersona', 'RESTRICT', 'CASCADE');
    }

    public function safeDown()
    {
        if ($this->hayInscripciones()) {
            echo "evento_inscripcion tiene datos: revertir los perdería.\n";
            return false;
        }

        $tabla = '{{%evento_inscripcion}}';
        $this->dropForeignKey('fk-evento_inscripcion-persona', $tabla);
        $this->dropForeignKey('fk-evento_inscripcion-desafio', $tabla);
        $this->dropIndex('uk_inscripcion_desafio_persona', $tabla);

        foreach (['iddesafio', 'estado_pago', 'estado_inscripcion', 'descuento', 'puesto', 'tiempo', 'observacion'] as $columna) {
            $this->dropColumn($tabla, $columna);
        }

        $this->addColumn($tabla, 'idevento', $this->integer()->notNull()->after('idinscripcion'));
        $this->addColumn($tabla, 'pagado', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn($tabla, 'monto', $this->decimal(10, 2)->notNull()->defaultValue(0));
        $this->addColumn($tabla, 'estado', $this->integer()->notNull());
        $this->addForeignKey('fk-evento_inscripcion-evento', $tabla, 'idevento', '{{%evento}}', 'idevento', 'CASCADE', 'CASCADE');

        $this->dropForeignKey('fk-evento_desafio_foto-desafio', '{{%evento_desafio_foto}}');
        $this->dropTable('{{%evento_desafio_foto}}');
        $this->dropForeignKey('fk-evento_desafio-evento', '{{%evento_desafio}}');
        $this->dropTable('{{%evento_desafio}}');
    }

    private function hayInscripciones(): bool
    {
        return (new Query())->from('{{%evento_inscripcion}}')->exists($this->db);
    }
}
