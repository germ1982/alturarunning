<?php

namespace app\controllers;

use app\models\SistemaLog;
use app\models\User;
use app\models\User_usuario_rol;
use app\models\UserSearch;
use Yii;
use \yii\web\Response;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * UserController implements the CRUD actions for User model.
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
                        // 1. Acceso para editar datos propios o si eres admin
                        [
                            'allow' => true,
                            'actions' => ['update', 'change_password'],
                            'matchCallback' => function ($rule, $action) {
                                if (Yii::$app->user->isGuest) return false;

                                $userLogueado = \app\models\User::findOne(Yii::$app->user->id);
                                // Captura ID por POST (AJAX) o por GET (URL normal)
                                $idAEditar = Yii::$app->request->post('iduser') ?: Yii::$app->request->get('id');

                                // Permite si es Admin O si está editando su propio registro
                                return $userLogueado->tieneRol('Administrador') || (Yii::$app->user->id == $idAEditar);
                            },
                        ],
                        // 2. Acceso exclusivo para administradores
                        [
                            'allow' => true,
                            'actions' => ['index', 'create', 'roles', 'view', 'activar', 'desactivar', 'contacto-modal', 'reset-password'],
                            'matchCallback' => function ($rule, $action) {
                                $userLogueado = \app\models\User::findOne(Yii::$app->user->id);
                                return $userLogueado !== null && $userLogueado->tieneRol('Administrador');
                            },
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => \yii\filters\VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all User models.
     *
     * @return string
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
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        // Seteamos el formato de respuesta a JSON
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = User::find()->where(['id' => $id])->with('usuarioRoles')->one();

        return [
            'title'   => "Detalles de: " . $model->username, // O el campo que uses
            'content' => $this->renderAjax('view', [
                'model' => $model,
            ]),
            'footer' => '<div class="d-flex justify-content-center w-100">' .
                Html::button('Cerrar', [
                    'class' => 'btn-custom',
                    'data-bs-dismiss' => 'modal'
                ]) .
                '</div>',
        ];
    }

    public function actionCreate()
    {
        // En lugar de findModel, creamos la instancia del formulario de registro
        $model = new \app\models\UserSignupForm();
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if ($this->request->isPost) {
            // Usamos el método signup() que ya tenés, que valida y guarda con roles
            if ($model->load($this->request->post()) && $model->signup()) {
                SistemaLog::registrar(
                    SistemaLog::MODULO_USUARIOS,
                    SistemaLog::ACCION_CREATE,
                    $model->id,
                    "Se Creo el usuario $model->username"
                );
                return [
                    'forceReload' => '#user-pjax-container',
                    'title' => "Creado con éxito",
                    'content' => '<div class="alert alert-success">El nuevo usuario fue registrado correctamente.</div>',
                    'footer' => '<div class="d-flex justify-content-center w-100">' .
                        Html::button('Cerrar', [
                            'class' => 'btn-custom',
                            'data-bs-dismiss' => 'modal'
                        ]) .
                        '</div>',
                ];
            }

            // Si falla la validación (ej: contraseñas no coinciden)
            return [
                'title' => "Error al Crear Usuario",
                'content' => $this->renderAjax('create', ['model' => $model]),
                'footer' =>
                Html::button('Cancelar', ['class' => 'btn-custom me-auto', 'data-bs-dismiss' => 'modal']) .
                    Html::button('Crear Usuario', [
                        'class' => 'btn-custom',
                        'type' => 'submit',
                        'form' => 'form-usuario' // Este ID debe estar en tu nuevo create.php
                    ])
            ];
        }

        // Apertura inicial (GET)
        return [
            'title' => "Registrar Nuevo Usuario",
            'content' => $this->renderAjax('create', ['model' => $model]),
            'footer' =>
            Html::button('Cancelar', ['class' => 'btn-custom me-auto', 'data-bs-dismiss' => 'modal']) .
                Html::button('Crear Usuario', [
                    'class' => 'btn-custom',
                    'type' => 'submit',
                    'form' => 'form-usuario'
                ])
        ];
    }


    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {

                SistemaLog::registrar(
                    SistemaLog::MODULO_USUARIOS,
                    SistemaLog::ACCION_UPDATE,
                    $model->id,
                    "Se Edito el usuario $model->username"
                );
                // CASO: ÉXITO AL GUARDAR
                return [
                    'forceReload' => '#user-pjax-container', // El ID que definimos en la grilla
                    'title' => "Actualizado con éxito",
                    'content' => '<div class="alert alert-success">Se guardaron los cambios correctamente.</div>',
                    'footer' => '<div class="d-flex justify-content-center w-100">' .
                        Html::button('Cerrar', [
                            'class' => 'btn-custom',
                            'data-bs-dismiss' => 'modal'
                        ]) .
                        '</div>',
                ];
            }

            // CASO: ERROR DE VALIDACIÓN (Vuelve a mostrar el form con los errores)
            return [
                'title' => "Error Al Editar Registro: " . $id,
                'content' => $this->renderAjax('update', ['model' => $model]),
                'footer' =>
                Html::button('Cancelar', ['class' => 'btn-custom me-auto', 'data-bs-dismiss' => 'modal']) .
                    Html::button('Guardar Cambios', [
                        'class' => 'btn-custom',
                        'type' => 'submit',
                        'form' => 'form-usuario' // Asegurate que coincida con el ID en _form.php
                    ])
            ];
        }

        // CASO: APERTURA INICIAL (GET)
        return [
            'title' => " Apertura Editar Registro: " . $id,
            'content' => $this->renderAjax('update', ['model' => $model]),
            'footer' =>
            Html::button('Cancelar', ['class' => 'btn-custom me-auto', 'data-bs-dismiss' => 'modal']) .
                Html::button('Guardar Cambios', [
                    'class' => 'btn-custom',
                    'type' => 'submit',
                    'form' => 'form-usuario' // <-- CLAVE: Esto vincula el botón al form
                ])
        ];
    }

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
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionChange_password()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $log = []; // Array para trackear pasos

        $pActual = $request->post('actual');
        $pNueva  = $request->post('nueva');
        $iduser  = $request->post('iduser');

        $log[] = "1. Datos recibidos: ID=$iduser";

        $user = $this->findModel($iduser);
        $userLogueado = \app\models\User::findOne(Yii::$app->user->id);

        $log[] = "2. Modelo usuario cargado";

        // Detectamos si el usuario se está modificando a sí mismo
        $esPropio = ($user->id == Yii::$app->user->id);

        // Bypass de contraseña actual: 
        // Si es Administrador modificando a OTRO usuario, no pedimos la contraseña actual.
        // Si es su propia contraseña (aunque sea admin), SÍ debe ingresar su contraseña actual.
        if ($userLogueado->tieneRol('Administrador') && !$esPropio) {
            $log[] = "3. Bypass de validación: Administrador modificando a otro usuario";
        } else {
            $log[] = "3. Validando contraseña actual...";
            if (!$user->validatePassword($pActual)) {
                return ['success' => false, 'message' => 'Contraseña actual incorrecta.', 'log' => $log];
            }
            $log[] = "3. Validación OK";
        }

        $user->setPassword($pNueva);
        $log[] = "4. Nueva contraseña asignada";

        if ($user->save()) {
            $log[] = "5. Guardado exitoso";
            SistemaLog::registrar(
                SistemaLog::MODULO_USUARIOS,
                SistemaLog::ACCION_UPDATE,
                $user->id,
                "Se Cambio Contraseña del usuario " . $user->username,
            );

            // Si es su propia clave, cerramos sesión y redirigimos al login
            if ($esPropio) {
                $log[] = "6. Es autocambio: Cerrando sesión";
                Yii::$app->user->logout();
                return [
                    'success' => true,
                    'redirect' => \yii\helpers\Url::to(['site/login']),
                    'is_self' => true,
                    'log' => $log
                ];
            }

            // Si un admin cambió la contraseña de otro, la sesión sigue abierta
            $log[] = "6. Es un admin cambiando a otro usuario: Sesión intacta";
            return [
                'success' => true,
                'message' => 'Contraseña actualizada con éxito.',
                'is_self' => false,
                'log' => $log
            ];
        } else {
            $log[] = "5. Error al guardar: " . json_encode($user->errors);
            return ['success' => false, 'message' => 'Error al guardar.', 'log' => $log];
        }
    }

    public function actionRoles($id)
    {
        $request = Yii::$app->request;
        $user = $this->findModel($id);

        // IDs de roles actuales del usuario
        $rolesActuales = User_usuario_rol::find()
            ->select('idrol')
            ->where(['idusuario' => $id])
            ->column();

        if ($request->isAjax) {
            //Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->format = Response::FORMAT_JSON;
            // =====================
            // ABRIR MODAL (GET)
            // =====================
            if ($request->isGet) {
                return [
                    'title'   => 'Editar roles de ' . Html::encode($user->username),
                    'content' => $this->renderAjax('_form_roles', [
                        'user' => $user,
                        'rolesActuales' => $rolesActuales,
                    ]),
                    'footer'  =>
                    Html::button('Cerrar', [
                        'id' => 'btnCerrar',
                        'class' => 'btn-custom me-auto',
                        'data-bs-dismiss' => 'modal'

                    ]) .
                        Html::button('Guardar', [
                            'id' => 'btnGuardar',
                            'class' => 'btn-custom',
                            'type' => 'submit',
                            'form' => 'roles-form', // <-- Este ID debe ser igual al del ActiveForm
                        ])
                ];
            }

            // =====================
            // GUARDAR (POST)
            // =====================
            $rolesPost = $request->post('roles', []);

            // Roles a agregar
            $rolesAgregar = array_diff($rolesPost, $rolesActuales);

            // Roles a eliminar
            $rolesEliminar = array_diff($rolesActuales, $rolesPost);

            // Insertar nuevos
            foreach ($rolesAgregar as $idrol) {
                $ur = new User_usuario_rol();
                $ur->idusuario = $id;
                $ur->idrol = $idrol;
                $ur->save(false);
            }

            // Eliminar quitados
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
            return [
                'forceReload' => '#user-pjax-container',
                'title' => 'Roles actualizados',
                'content' => '<span class="text-success">Roles guardados correctamente</span>',
                'footer' => '<div class="d-flex justify-content-center w-100">' .
                    Html::button('Cerrar', [
                        'class' => 'btn-custom',
                        'data-bs-dismiss' => 'modal'
                    ]) .
                    '</div>',
            ];
        }

        // Seguridad: si entran sin ajax
        throw new \yii\web\BadRequestHttpException('Acceso inválido');
    }

    public function actionDesactivar($id)
    {
        $model = $this->findModel($id);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if ($this->request->isPost) { // Solo cambia el status si viene por POST
            $model->status = 0; // Estado Desactivado
            if ($model->save(false)) {
                SistemaLog::registrar(
                    SistemaLog::MODULO_USUARIOS,
                    SistemaLog::ACCION_UPDATE,
                    $model->id,
                    "Se Desactivo usuario " . $model->username,
                );
                return [
                    'forceReload' => '#user-pjax-container',
                    'title' => '<span class="d-flex justify-content-center w-100">Usuario Desactivado</span>',
                    'content' => '<span class="d-flex justify-content-center w-100 alert alert-warning">El usuario ha sido desactivado correctamente</span>',
                    'footer' => '<div class="d-flex justify-content-center w-100">' .
                        Html::button('Cerrar', [
                            'class' => 'btn-custom',
                            'data-bs-dismiss' => 'modal'
                        ]) .
                        '</div>',
                ];
            }
        }

        // Si entra por GET (al hacer clic en la grilla), muestra la pregunta
        return [
            'title' => "Confirmar Desactivación",
            'content' => "¿Estás seguro que querés desactivar a <b>" . $model->username . "</b>?",
            'footer' => Html::beginForm(['user/desactivar', 'id' => $id], 'post', ['class' => 'w-100']) .
                '<div class="d-flex justify-content-between w-100">' .
                Html::button('Cancelar', [
                    'class' => 'btn-custom',
                    'data-bs-dismiss' => 'modal'
                ]) .
                Html::submitButton('Sí, desactivar', [
                    'class' => 'btn-custom' // Podrías sumarle un 'btn-danger' si querés color rojo
                ]) .
                '</div>' .
                Html::endForm()
        ];
    }

    public function actionActivar($id)
    {
        $model = $this->findModel($id);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if ($this->request->isPost) { // Solo cambia el status si viene por POST
            $model->status = 10;
            if ($model->save(false)) {
                SistemaLog::registrar(
                    SistemaLog::MODULO_USUARIOS,
                    SistemaLog::ACCION_REACTIVAR,
                    $model->id,
                    "Se Activo usuario " . $model->username,
                );
                return [
                    'forceReload' => '#user-pjax-container',
                    'title' => '<span class="d-flex justify-content-center w-100">Usuario Activado</span>',
                    'content' => '<span class="d-flex justify-content-center w-100 alert alert-success">Usuario activado correctamente</span>',
                    'footer' => '<div class="d-flex justify-content-center w-100">' .
                        Html::button('Cerrar', [
                            'class' => 'btn-custom',
                            'data-bs-dismiss' => 'modal'
                        ]) .
                        '</div>',

                ];
            }
        }

        // Si entra por GET (al hacer clic en la grilla), muestra la pregunta
        return [
            'title' => "Confirmar Activación",
            'content' => "¿Estás seguro que querés activar a <b>" . $model->username . "</b>?",
            'footer' => Html::beginForm(['user/activar', 'id' => $id], 'post', ['class' => 'w-100']) .
                '<div class="d-flex justify-content-between w-100">' .
                Html::button('Cancelar', [
                    'class' => 'btn-custom',
                    'data-bs-dismiss' => 'modal'
                ]) .
                Html::submitButton('Sí, activar', [
                    'class' => 'btn-custom'
                ]) .
                '</div>' .
                Html::endForm()
        ];
    }

    public function actionContactoModal($id)
    {
        $model = $this->findModel($id);
        $num = preg_replace('/[^0-9]/', '', $model->telefono);

        // Ajuste para Argentina (WhatsApp prefiere 549...)
        $waNum = str_starts_with($num, '54') ? $num : '549' . $num;
        // Para llamadas normales, con el +54 está bien
        $callNum = str_starts_with($num, '54') ? '+' . $num : '+54' . $num;

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'title' => 'Opciones para ' . $model->username,
            'content' => $this->renderPartial('_form_contacto_opciones', [
                'waNum' => $waNum,
                'callNum' => $callNum,
            ]),
            'footer' => '<div class="d-flex justify-content-center w-100">' .
                Html::button('Cerrar', [
                    'class' => 'btn-custom',
                    'data-bs-dismiss' => 'modal'
                ]) .
                '</div>',
        ];
    }

    public function actionResetPassword($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if ($this->request->isPost) {
                $model->setPassword('123456');
                if ($model->save(false)) {
                    SistemaLog::registrar(
                        SistemaLog::MODULO_USUARIOS,
                        SistemaLog::ACCION_UPDATE,
                        $model->id,
                        "Se Reseteo Contraseña del usuario " . $model->username,
                    );
                    return [
                        'forceReload' => '#user-pjax-container',
                        'title' => '¡Reseteado!',
                        'content' => '<div class="alert alert-success text-center">La contraseña de <b>' . $model->username . '</b> se restableció a 123456.</div>',
                        'footer' => '<div class="d-flex justify-content-center w-100">' .
                            Html::button('Cerrar', ['class' => 'btn-custom', 'data-bs-dismiss' => 'modal']) .
                            '</div>',
                    ];
                }
            }

            return [
                'title' => "Confirmar Reset",
                'content' => "¿Estás seguro que querés resetear la contraseña de <b>" . $model->username . "</b> a 123456?",
                'footer' => Html::beginForm(['user/reset-password', 'id' => $id], 'post', ['class' => 'w-100']) .
                    '<div class="d-flex justify-content-between w-100">' .
                    Html::button('Cancelar', ['class' => 'btn-custom me-auto', 'data-bs-dismiss' => 'modal']) .
                    Html::submitButton('Sí, resetear', ['class' => 'btn-custom']) .
                    '</div>' .
                    Html::endForm(),
            ];
        }

        throw new \yii\web\BadRequestHttpException('Acceso inválido');
    }
}
