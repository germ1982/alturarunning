<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class RolController extends Controller
{
    public function actionCrear($nombre = null)
    {
        $nombre = trim($nombre);
        if (empty($nombre)) {
            return $this->asJson(['success' => false, 'mensaje' => 'El nombre del rol no puede estar vacío']);
        }

        $auth = Yii::$app->authManager;

        if ($auth->getRole($nombre)) {
            return $this->asJson(['success' => false, 'mensaje' => "El rol '$nombre' ya existe"]);
        }

        $role = $auth->createRole($nombre);
        $auth->add($role);

        return $this->asJson(['success' => true, 'mensaje' => "Rol $nombre creado correctamente"]);
    }


    public function actionVerificar($nombre)
    {
        $nombre = trim($nombre);
        if (empty($nombre)) {
            return $this->asJson(['existe' => false]);
        }
        $auth = Yii::$app->authManager;
        return $this->asJson(['existe' => ($auth->getRole($nombre) !== null)]);
    }
    public function actionIndex()
    {
        $auth = Yii::$app->authManager;
        $roles = $auth->getRoles();

        return $this->render('index', [
            'roles' => $roles,
        ]);
    }

    public function actionUpdate($nombreOriginal, $nuevoNombre)
    {
        $nombreOriginal = trim($nombreOriginal);
        $nuevoNombre = trim($nuevoNombre);

        if (empty($nuevoNombre)) {
            return $this->asJson(['success' => false, 'mensaje' => 'El nuevo nombre no puede estar vacío']);
        }

        $auth = Yii::$app->authManager;
        $role = $auth->getRole($nombreOriginal);

        if (!$role) {
            return $this->asJson(['success' => false, 'mensaje' => "El rol '$nombreOriginal' no existe"]);
        }

        if ($nombreOriginal !== $nuevoNombre && $auth->getRole($nuevoNombre)) {
            return $this->asJson(['success' => false, 'mensaje' => "El rol '$nuevoNombre' ya existe"]);
        }

        $role->name = $nuevoNombre;
        $auth->update($nombreOriginal, $role);

        return $this->asJson(['success' => true, 'mensaje' => "Rol actualizado correctamente"]);
    }

    public function actionDelete($nombre)
    {
        $nombre = trim($nombre);
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($nombre);

        if (!$role) {
            return $this->asJson(['success' => false, 'mensaje' => "El rol '$nombre' no existe"]);
        }

        $auth->remove($role);

        return $this->asJson(['success' => true, 'mensaje' => "Rol '$nombre' eliminado correctamente"]);
    }
}
