<?php

namespace app\controllers;

use app\models\SistemaLog;
use app\models\User;
use app\models\User_usuario_rol;
use app\models\UserSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * UserController implementa las acciones CRUD para el modelo User sin modales.
 */
class UserController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => \yii\filters\AccessControl::class,
                    'denyCallback' => function ($rule, $action) {
                        return $this->redirect(['site/index']);
                    },
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['update', 'change_password'],
                            'matchCallback' => function ($rule, $action) {
                                if (Yii::$app->user->isGuest) return false;

                                $userLogueado = \app\models\User::findOne(Yii::$app->user->id);
                                $idAEditar = Yii::$app->request->post('iduser') ?: Yii::$app->request->get('id');

                                return $userLogueado->tieneRol('Administrador') || (Yii::$app->user->id == $idAEditar);
                            },
                        ],
                        [
                            'allow' => true,
                            'actions' => ['index', 'create', 'roles', 'view', 'activar', 'desactivar', 'contacto', 'reset-password'],
                            'matchCallback' => function ($rule, $action) {
                                $userLogueado = \app\models\User::findOne(Yii::$app->user->id);
                                return $userLogueado !== null && $userLogueado->tieneRol('Administrador');
                            },
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                        'desactivar' => ['POST'],
                        'activar' => ['POST'],
                        'reset-password' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all User models.
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     */
    public function actionView($id)
    {
        $model = User::find()->where(['id' => $id])->with('usuarioRoles')->one();
        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new User model.
     * Creates a new User model.
     */
    public function actionCreate()
    {
        $model = new \app\models\UserSignupForm();

        if ($this->request->isPost) {
            // Carga los datos del post al formulario y ejecuta el método signup()
            if ($model->load($this->request->post()) && $model->signup()) {
                // Si todo salió bien, te manda al listado
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                SistemaLog::registrar(
                    SistemaLog::MODULO_USUARIOS,
                    SistemaLog::ACCION_UPDATE,
                    $model->id,
                    "Se Edito el usuario $model->username"
                );
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     */
    public function actionDelete($id)
    {
        $nombre = User::findOne($id)->username;
        $this->findModel($id)->delete();
        SistemaLog::registrar(
            SistemaLog::MODULO_USUARIOS,
            SistemaLog::ACCION_DELETE,
            $id,
            "Se elimino el usuario " . $nombre,
        );

        return $this->redirect(['index']);
    }

    /**
     * Change user password.
     */
    public function actionChange_password()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $log = [];

        $pActual = $request->post('actual');
        $pNueva  = $request->post('nueva');
        $iduser  = $request->post('iduser');

        $user = $this->findModel($iduser);
        $userLogueado = \app\models\User::findOne(Yii::$app->user->id);
        $esPropio = ($user->id == Yii::$app->user->id);

        if ($userLogueado->tieneRol('Administrador') && !$esPropio) {
            // Bypass
        } else {
            if (!$user->validatePassword($pActual)) {
                return ['success' => false, 'message' => 'Contraseña actual incorrecta.'];
            }
        }

        $user->setPassword($pNueva);

        if ($user->save()) {
            SistemaLog::registrar(
                SistemaLog::MODULO_USUARIOS,
                SistemaLog::ACCION_UPDATE,
                $user->id,
                "Se Cambio Contraseña del usuario " . $user->username,
            );

            if ($esPropio) {
                Yii::$app->user->logout();
                return [
                    'success' => true,
                    'redirect' => Url::to(['site/login']),
                    'is_self' => true
                ];
            }

            return [
                'success' => true,
                'message' => 'Contraseña actualizada con éxito.',
                'is_self' => false
            ];
        } else {
            return ['success' => false, 'message' => 'Error al guardar.'];
        }
    }

    /**
     * Manage user roles.
     */
    public function actionRoles($id)
    {
        $request = Yii::$app->request;
        $user = $this->findModel($id);

        $rolesActuales = User_usuario_rol::find()
            ->select('idrol')
            ->where(['idusuario' => $id])
            ->column();

        if ($request->isPost) {
            $rolesPost = $request->post('roles', []);
            $rolesAgregar = array_diff($rolesPost, $rolesActuales);
            $rolesEliminar = array_diff($rolesActuales, $rolesPost);

            foreach ($rolesAgregar as $idrol) {
                $ur = new User_usuario_rol();
                $ur->idusuario = $id;
                $ur->idrol = $idrol;
                $ur->save(false);
            }

            if (!empty($rolesEliminar)) {
                User_usuario_rol::deleteAll([
                    'idusuario' => $id,
                    'idrol' => $rolesEliminar
                ]);
            }

            SistemaLog::registrar(
                SistemaLog::MODULO_USUARIOS,
                SistemaLog::ACCION_UPDATE,
                $user->id,
                "Se Editaron Roles del usuario " . $user->username,
            );
            return $this->redirect(['index']);
        }

        return $this->render('roles', [
            'user' => $user,
            'rolesActuales' => $rolesActuales,
        ]);
    }

    /**
     * Deactivate user.
     */
    public function actionDesactivar($id)
    {
        $model = $this->findModel($id);
        $model->status = 0;
        if ($model->save(false)) {
            SistemaLog::registrar(
                SistemaLog::MODULO_USUARIOS,
                SistemaLog::ACCION_UPDATE,
                $model->id,
                "Se Desactivo usuario " . $model->username,
            );
        }
        return $this->redirect(['index']);
    }

    /**
     * Activate user.
     */
    public function actionActivar($id)
    {
        $model = $this->findModel($id);
        $model->status = 10;
        if ($model->save(false)) {
            SistemaLog::registrar(
                SistemaLog::MODULO_USUARIOS,
                SistemaLog::ACCION_REACTIVAR,
                $model->id,
                "Se Activo usuario " . $model->username,
            );
        }
        return $this->redirect(['index']);
    }

    /**
     * Contact options view.
     */
    public function actionContacto($id)
    {
        $model = $this->findModel($id);

        // Validamos que exista la persona y tenga teléfono asignado
        $telefono = ($model->persona && $model->persona->telefono) ? $model->persona->telefono : '';

        $num = preg_replace('/[^0-9]/', '', $telefono);
        $waNum = str_starts_with($num, '54') ? $num : '549' . $num;
        $callNum = str_starts_with($num, '54') ? '+' . $num : '+54' . $num;

        return $this->render('contacto', [
            'model' => $model,
            'waNum' => $waNum,
            'callNum' => $callNum,
        ]);
    }

    /**
     * Reset user password to default.
     */
    public function actionResetPassword($id)
    {
        $model = $this->findModel($id);
        $model->setPassword('123456');
        if ($model->save(false)) {
            SistemaLog::registrar(
                SistemaLog::MODULO_USUARIOS,
                SistemaLog::ACCION_UPDATE,
                $model->id,
                "Se Reseteo Contraseña del usuario " . $model->username,
            );
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
