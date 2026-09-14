<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
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
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
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

    public function actionEventos(): string
    {
        return $this->render('en_construccion');
    }

    public function actionAlumnos(): string
    {
        return $this->render('en_construccion');
    }

    public function actionPanel_administrar()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']); // Al login (Inicio de sesión)
        }
        return $this->render('panel_control');
    }

    public function actionGestionar_alumnos(): string
    {
        return $this->render('en_construccion');
    }

    public function actionGestionar_eventos(): string
    {
        return $this->render('en_construccion');
    }
    public function actionGestionar_contenidos(): string
    {
        return $this->render('en_construccion');
    }

    public function actionGestionar_usuarios()
    {
        return $this->redirect(['user/index']);
    }

    public function actionGestionar_profesores(): string
    {
        return $this->render('en_construccion');
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

        $modulo = Yii::$app->request->post('modulo');
        $accion = Yii::$app->request->post('accion');
        $descripcion = Yii::$app->request->post('descripcion');

        $guardado = SistemaLog::registrar($modulo, $accion, null, $descripcion);

        return ['success' => $guardado];
    }
}
