<?php
namespace app\helpers;

use Yii;

class UserHelper
{
    public static function esAdmin(): bool
    {
        return static::tieneRol(1); // idrol = 1 Administrador
    }

    public static function esModerador(): bool
    {
        return static::tieneRol(4);
    }

    public static function tieneRol(int $idrol): bool
    {
        if (Yii::$app->user->isGuest) return false;
        $id = Yii::$app->user->id;
        return (bool) \app\models\User_usuario_rol::find()
            ->where(['idusuario' => $id, 'idrol' => $idrol])
            ->exists();
    }

    public static function puedeEditar($idusuario): bool
    {
        if (Yii::$app->user->isGuest) return false;
        return Yii::$app->user->id == $idusuario || static::esAdmin() || static::esModerador();
    }
}