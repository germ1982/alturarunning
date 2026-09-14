<?php

use yii\db\Migration;

class m260914_034417_create_user_and_roles_tables extends Migration
{
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';

        // Tabla User
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'password_hash' => $this->string()->notNull(),
            'access_token' => $this->string(),
            'auth_key' => $this->string()->notNull(),
            'idpersona' => $this->string(15)->unique(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'status' => $this->tinyInteger(3)->notNull()->defaultValue(10)->comment('0=Inactivo, 1=Activo'),
        ], $tableOptions);

        // Tabla user_rol
        $this->createTable('{{%user_rol}}', [
            'idrol' => $this->primaryKey(),
            'nombre' => $this->string(50)->notNull(),
            'descripcion' => $this->string(150),
            'activo' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        // Tabla intermedia user_usuario_rol (Muchos a Muchos)
        $this->createTable('{{%user_usuario_rol}}', [
            'id' => $this->primaryKey(),
            'idusuario' => $this->integer()->notNull(),
            'idrol' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        // Índices y Foreign Keys
        $this->createIndex('uk_usuario_rol', '{{%user_usuario_rol}}', ['idusuario', 'idrol'], true);
        $this->addForeignKey('fk_urol_usuario', '{{%user_usuario_rol}}', 'idusuario', '{{%user}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_urol_rol', '{{%user_usuario_rol}}', 'idrol', '{{%user_rol}}', 'idrol', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_urol_rol', '{{%user_usuario_rol}}');
        $this->dropForeignKey('fk_urol_usuario', '{{%user_usuario_rol}}');
        $this->dropTable('{{%user_usuario_rol}}');
        $this->dropTable('{{%user_rol}}');
        $this->dropTable('{{%user}}');
    }
}
