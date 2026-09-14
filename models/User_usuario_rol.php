<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_usuario_rol".
 *
 * @property int $id
 * @property int $idusuario
 * @property int $idrol
 * @property string|null $created_at
 *
 * @property UserRol $idrol0
 * @property User $idusuario0
 */
class User_usuario_rol extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_usuario_rol';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idusuario', 'idrol'], 'required'],
            [['idusuario', 'idrol'], 'integer'],
            [['created_at'], 'safe'],
            [['idusuario', 'idrol'], 'unique', 'targetAttribute' => ['idusuario', 'idrol']],
            [['idrol'], 'exist', 'skipOnError' => true, 'targetClass' => User_rol::class, 'targetAttribute' => ['idrol' => 'idrol']],
            [['idusuario'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['idusuario' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idusuario' => 'Idusuario',
            'idrol' => 'Idrol',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Idrol0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIdrol0()
    {
        return $this->hasOne(User_rol::class, ['idrol' => 'idrol']);
    }

    /**
     * Gets query for [[Idusuario0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIdusuario0()
    {
        return $this->hasOne(User::class, ['id' => 'idusuario']);
    }

}
