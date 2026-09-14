<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $password_hash
 * @property int|null $idpersona
 * @property int $status
 * @property string|null $auth_key
 * @property string|null $access_token
 *
 * @property Persona $persona
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE  = 10;

    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    //Busca un usuario por ID (solo si está activo).
    public static function findIdentity($id)
    {
        return static::findOne([
            'id' => $id,
            'status' => self::STATUS_ACTIVE,
        ]);
    }


    /**
     * {@inheritdoc}
     */

    //Autenticación vía token (por ejemplo API).
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne([
            'access_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Relación con la tabla persona
     */
    public function getPersona()
    {
        return $this->hasOne(Persona::class, ['idpersona' => 'idpersona']);
    }

          /* public static function findByUsername($username)
      {
            return static::findOne([
                  'username' => $username,
                  'status' => self::STATUS_ACTIVE,
            ]);
      } */
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    // ---------------------------------------------------------
    //  BEFORE SAVE — Manejo automático de seguridad
    // ---------------------------------------------------------
public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            return true;
        }
        return false;
    }

    public function getUsuarioRoles()
    {
        return $this->hasMany(User_usuario_rol::class, ['idusuario' => 'id']);
    }

    public function getRoles()
    {
        return $this->hasMany(User_rol::class, ['idrol' => 'idrol'])
            ->via('usuarioRoles');
    }

    public function tieneRol($nombreRol)
    {
        return $this->getRoles()
            ->andWhere(['nombre' => $nombreRol, 'activo' => 1])
            ->exists();
    }
}
