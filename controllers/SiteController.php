<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\helpers\TemaHelper;
use app\helpers\UserHelper;
use app\models\ContactForm;
use app\models\LoginForm;
use app\models\SistemaLog;
use app\models\UserSignupForm;
use yii\captcha\CaptchaAction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\base\Security;
use yii\mail\MailerInterface;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

class SiteController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly MailerInterface $mailer,
        private readonly Security $security,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => [
                    'logout',
                    'panel_administrar',
                    'registrar_log_ajax',
                    'gestionar_usuarios',
                    'gestionar_contenidos',
                    'gestionar_eventos',
                    'gestionar_alumnos',
                    'gestionar_profesores',
                    'aspecto',
                ],
                'rules' => [
                    [
                        'actions' => ['logout', 'panel_administrar', 'registrar_log_ajax'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    $this->reglaPermiso('gestionar_usuarios', 'usuarios'),
                    $this->reglaPermiso('gestionar_contenidos', 'contenidos'),
                    $this->reglaPermiso('gestionar_eventos', 'eventos'),
                    $this->reglaPermiso('gestionar_alumnos', 'alumnos'),
                    $this->reglaPermiso('gestionar_profesores', 'profesores'),
                    $this->reglaPermiso('aspecto', 'aspecto'),
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                    'registrar_log_ajax' => ['post'],
                ],
            ],
        ];
    }

    private function reglaPermiso(string $accion, string $permiso): array
    {
        return [
            'actions' => [$accion],
            'allow' => true,
            'matchCallback' => static fn () => UserHelper::puede($permiso),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'transparent' => true,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        return $this->render('index');
    }

        public function actionSolicitar_reset()
    {
        return $this->render('solicitar_reset');
    }
    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin(): Response|string
{
      // 1. Si el usuario ya inició sesión (no es invitado), lo redirige al inicio.
      if (!Yii::$app->user->isGuest) {
            return $this->goHome();
      }

      // 2. Instancia un nuevo objeto del formulario de login.
      $model = new LoginForm();

      // 3. Carga los datos POST del formulario y valida/ejecuta el inicio de sesión.
      if ($model->load($this->request->post()) && $model->login()) {

            // 4. Registra en el log del sistema el evento exitoso de inicio de sesión.
            SistemaLog::registrar(
                SistemaLog::MODULO_USUARIOS,
                SistemaLog::ACCION_INICIO_SESION,
                Yii::$app->user->id,
                "Inicio de Sesion de usuario " . Yii::$app->user->identity->username
            );

            // 5. Redirige al usuario a la página previa que estaba intentando visitar.
            return $this->goBack();
      }

      // Los intentos fallidos quedan en la auditoría (nunca la contraseña).
      if ($this->request->isPost && trim((string) $model->username) !== '') {
            SistemaLog::registrar(
                SistemaLog::MODULO_USUARIOS,
                SistemaLog::ACCION_LOGIN_FALLIDO,
                $model->getUser()->id ?? null,
                'Intento fallido de inicio de sesión con: ' . mb_substr(trim((string) $model->username), 0, 100)
            );
      }

      // 6. Limpia el campo de contraseña por seguridad si falla el login.
      $model->password = '';

      // 7. Renderiza de nuevo la vista de login pasándole el modelo con los errores.
      return $this->render('login', ['model' => $model]);
}

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact(): Response|string
    {

        return $this->render('en_construccion');
        $model = new ContactForm();

        $contact = $model->load($this->request->post()) && $model->contact(
            $this->mailer,
            Yii::$app->params['adminEmail'],
            Yii::$app->params['senderEmail'],
            Yii::$app->params['senderName'],
        );

        if ($contact) {
            Yii::$app->session->setFlash(
                'success',
                'Thank you for contacting us. We will respond to you as soon as possible.',
            );

            return $this->refresh();
        }

        return $this->render('contact', ['model' => $model]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout(): string
    {
        return $this->render('about');
    }

    public function actionEventos(): Response
    {
        return $this->redirect(['evento/listado']);
    }

    public function actionAlumnos(): Response
    {
        return $this->redirect(['alumno/listado']);
    }

    public function actionPanel_administrar()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']); // Al login (Inicio de sesión)
        }
        return $this->render('panel_control');
    }

    public function actionGestionar_alumnos(): Response
    {
        return $this->redirect(['alumno/index']);
    }

    public function actionGestionar_eventos(): Response
    {
        return $this->redirect(['evento/index']);
    }
    public function actionGestionar_contenidos(): Response
    {
        return $this->redirect(['contenido/index']);
    }

    public function actionGestionar_usuarios()
    {
        return $this->redirect(['user/index']);
    }

    public function actionGestionar_profesores(): Response
    {
        return $this->redirect(['profesor/index']);
    }


    /**
     * Elegir el tema visual de todo el sitio.
     */
    public function actionAspecto()
    {
        $actual = TemaHelper::actual();
        $elegido = (string) $this->request->post('tema');

        if ($this->request->isPost && isset(TemaHelper::TEMAS[$elegido])) {
            if ($elegido !== $actual) {
                TemaHelper::guardar($elegido);
                SistemaLog::registrar(
                    SistemaLog::MODULO_DATOS,
                    SistemaLog::ACCION_UPDATE,
                    null,
                    'Se cambió el aspecto del sitio: ' . TemaHelper::nombre($actual) . ' → ' . TemaHelper::nombre($elegido)
                );
            }

            Yii::$app->session->setFlash('success', 'El sitio ahora usa el tema ' . TemaHelper::nombre($elegido) . '.');

            return $this->redirect(['aspecto']);
        }

        return $this->render('aspecto', ['actual' => $actual]);
    }

    public function actionRegistro()
    {
        $model = new UserSignupForm();

        if ($model->load(Yii::$app->request->post()) && $user = $model->signup()) {
            if (Yii::$app->getUser()->login($user)) {
                Yii::$app->session->setFlash('success', 'Tu cuenta se creó correctamente.');
            } else {
                Yii::$app->session->setFlash('error', 'Hubo un error al iniciar sesión con tu nueva cuenta.');
            }

            return $this->goHome();
        }

        return $this->render('registro', [
            'model' => $model,
        ]);
    }

    public function actionRegistrar_log_ajax()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Para registrar desde JavaScript algo que solo pasa en el navegador. Queda a nombre
        // del usuario logueado y marcado como enviado desde el navegador.
        $modulo = (int) Yii::$app->request->post('modulo');
        $accion = (int) Yii::$app->request->post('accion');
        $descripcion = trim((string) Yii::$app->request->post('descripcion'));

        if (!isset(SistemaLog::MODULOS[$modulo]) || !isset(SistemaLog::ACCIONES[$accion]) || $descripcion === '') {
            return ['success' => false, 'message' => 'Módulo, acción o descripción inválidos.'];
        }

        $guardado = SistemaLog::registrar($modulo, $accion, null, '[navegador] ' . mb_substr($descripcion, 0, 1000));

        return ['success' => $guardado];
    }
}
