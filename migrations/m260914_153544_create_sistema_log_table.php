<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sistema_log}}`.
 */
class m260914_153544_create_sistema_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sistema_log}}', [
            'id' => $this->primaryKey(),
            'idusuario' => $this->integer()->null(),
            'idmodulo' => $this->integer()->notNull(),
            'idaccion' => $this->integer()->notNull(),
            'id_registro_afectado' => $this->integer()->null(),
            'descripcion' => $this->text()->notNull(),
            'fecha' => $this->dateTime()->notNull(),
        ]);

        // Clave foránea hacia la tabla user
        $this->addForeignKey(
            'fk_sistema_log_usuario',
            '{{%sistema_log}}',
            'idusuario',
            '{{%user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_sistema_log_usuario', '{{%sistema_log}}');
        $this->dropTable('{{%sistema_log}}');
    }
}