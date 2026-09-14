<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_rol".
 *
 * @property int $idrol
 * @property string $nombre
 * @property string|null $descripcion
 * @property int|null $activo
 * @property string|null $created_at
 *
 * @property User[] $idusuarios
 * @property UserUsuarioRol[] $userUsuarioRols
 */
class User_rol extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_rol';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descripcion'], 'default', 'value' => null],
            [['activo'], 'default', 'value' => 1],
            [['nombre'], 'required'],
            [['activo'], 'integer'],
            [['created_at'], 'safe'],
            [['nombre'], 'string', 'max' => 50],
            [['descripcion'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'nombre' => 'Nombre',
            'descripcion' => 'Descripcion',
            'activo' => 'Activo',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Idusuarios]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIdusuarios()
    {
        return $this->hasMany(User::class, ['id' => 'idusuario'])->viaTable('user_usuario_rol', ['idrol' => 'idrol']);
    }

    /**
     * Gets query for [[UserUsuarioRols]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserUsuarioRols()
    {
        return $this->hasMany(User_usuario_rol::class, ['idrol' => 'idrol']);
    }

}
