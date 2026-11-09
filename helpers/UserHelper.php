<?php
namespace app\helpers;

use Yii;

class UserHelper
{
    const ROL_ADMIN = 'Administrador';
    const ROL_PROFESOR = 'Profesor';
    const ROL_COLABORADOR = 'Colaborador';
    const ROL_COORDINADOR_EVENTOS = 'Coordinador de Eventos';

    /** Qué roles pueden entrar a cada sección de administración. */
    const PERMISOS = [
        'usuarios' => [self::ROL_ADMIN],
        'contenidos' => [self::ROL_ADMIN, self::ROL_PROFESOR, self::ROL_COLABORADOR],
        'eventos' => [self::ROL_ADMIN, self::ROL_PROFESOR, self::ROL_COLABORADOR, self::ROL_COORDINADOR_EVENTOS],
        'alumnos' => [self::ROL_ADMIN, self::ROL_PROFESOR],
        'profesores' => [self::ROL_ADMIN],
    ];

    public static function esAdmin(): bool
    {
        return static::tieneRol(self::ROL_ADMIN);
    }

    /**
     * @param string|string[] $nombres uno o varios nombres de rol
     */
    public static function tieneRol($nombres): bool
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        return Yii::$app->user->identity->tieneRol($nombres);
    }

    public static function puede(string $permiso): bool
    {
        return static::tieneRol(self::PERMISOS[$permiso] ?? []);
    }

    public static function puedeEditar($idusuario): bool
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        return Yii::$app->user->id == $idusuario || static::esAdmin();
    }
}
