<?php

namespace app\controllers;

use app\helpers\ImagenHelper;
use app\helpers\UserHelper;
use app\models\Calendario;
use app\models\ConstantesGlobales as C;
use app\models\Cuota;
use app\models\DatoSistema;
use app\models\DocumentacionTipo;
use app\models\Objetivo;
use app\models\PersonaInstructor;
use app\models\SistemaLog;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\validators\ImageValidator;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Administrar > Contenidos: todo lo que se ve en el sitio y se carga desde la base.
 */
class ContenidoController extends Controller
{
    private const CARPETA_BANNER = 'images/sistema/carrusel_banner';
    private const CARPETA_DESTACADOS = 'images/sistema/destacados';
    private const CARPETA_ABOUT = 'images/sistema/about';

    /** Listas simples de dato_sistema que se editan con el mismo formulario. */
    private const LISTAS = [
        C::ABOUT_PARRAFO => [
            'titulo' => 'Acerca de',
            'campo' => 'Título',
            'extra' => 'Texto',
            'ayuda' => 'Cada párrafo de la página "Acerca de", con su título.',
        ],
        C::TIPO_FRASES_DINO => [
            'titulo' => 'Frases del dino',
            'campo' => 'Frase',
            'extra' => null,
            'ayuda' => 'Se muestran rotando en la tarjeta del dino motivador.',
        ],
        C::TIPO_LUGAR_ENTRENAMIENTO => [
            'titulo' => 'Lugares de entrenamiento',
            'campo' => 'Lugar',
            'extra' => null,
            'ayuda' => 'Se eligen al cargar los horarios.',
        ],
        C::TIPO_TIPO_CLASE_ENTRENAMIENTO => [
            'titulo' => 'Modalidades de clase',
            'campo' => 'Modalidad',
            'extra' => null,
            'ayuda' => 'Se eligen al cargar los horarios.',
        ],
        C::TIPO_ESPECIALIDAD_INSTRUCTOR => [
            'titulo' => 'Especialidades de profesores',
            'campo' => 'Especialidad',
            'extra' => null,
            'ayuda' => 'Las especialidades que puede tener un profesor.',
        ],
    ];

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => static fn () => UserHelper::puede('contenidos'),
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'banner-eliminar' => ['POST'],
                    'destacado-eliminar' => ['POST'],
                    'dato-eliminar' => ['POST'],
                    'horario-eliminar' => ['POST'],
                    'about-imagen' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $contar = static fn (int $tipo) => (int) DatoSistema::find()->where(['idtipo' => $tipo])->count();
        $secciones = [
            ['Banner del home', 'Fotos que rotan arriba del home.', 'bi-images', ['banner'], $contar(C::TIPO_IMAGEN_BANNER) . ' fotos'],
            ['Destacados', 'Videos o fotos de la tarjeta Destacados.', 'bi-star', ['destacados'], $contar(C::TIPO_DESTACADO) . ' destacados'],
            ['Horarios', 'Días, horarios, lugares y profesores.', 'bi-clock', ['horarios'], Calendario::find()->count() . ' horarios'],
        ];

        foreach (self::LISTAS as $tipo => $lista) {
            $secciones[] = [$lista['titulo'], $lista['ayuda'], 'bi-list-ul', ['lista', 'tipo' => $tipo], $contar($tipo) . ' cargados'];
        }

        // Catálogos que pertenecen a la gestión de alumnos.
        if (UserHelper::puede('alumnos')) {
            $secciones[] = ['Cuotas', 'Meses, tipos y montos.', 'bi-cash-coin', ['cuota/index'], Cuota::find()->count() . ' cuotas'];
            $secciones[] = ['Objetivos', 'Objetivos y su puntaje.', 'bi-bullseye', ['objetivo/index'], Objetivo::find()->count() . ' objetivos'];
            $secciones[] = ['Tipos de documentación', 'Apto médico, DNI y otros.', 'bi-folder2-open', ['documentacion-tipo/index'], DocumentacionTipo::find()->count() . ' tipos'];
        }

        return $this->render('index', ['secciones' => $secciones]);
    }

    /* ---------------------------------------------------------------- Banner */

    public function actionBanner()
    {
        if ($this->request->isPost) {
            $subidas = 0;
            $errores = [];

            foreach (UploadedFile::getInstancesByName('imagenes') as $archivo) {
                $nombre = $this->guardarImagen($archivo, self::CARPETA_BANNER, $errores);

                if ($nombre !== null) {
                    (new DatoSistema(['idtipo' => C::TIPO_IMAGEN_BANNER, 'dato' => $nombre]))->save(false);
                    $subidas++;
                }
            }

            if ($subidas > 0) {
                SistemaLog::registrar(SistemaLog::MODULO_DATOS, SistemaLog::ACCION_CREATE, null, 'Se agregaron ' . $subidas . ' fotos al banner');
                Yii::$app->session->setFlash('success', 'Se agregaron ' . $subidas . ' fotos al banner.');
            }

            if ($errores !== []) {
                Yii::$app->session->setFlash('error', $errores);
            }

            return $this->redirect(['banner']);
        }

        return $this->render('banner', ['imagenes' => DatoSistema::deTipo(C::TIPO_IMAGEN_BANNER)]);
    }

    public function actionBannerEliminar($id)
    {
        $imagen = $this->buscarDato($id, C::TIPO_IMAGEN_BANNER);
        $imagen->delete();
        $this->borrarImagen(self::CARPETA_BANNER, $imagen->dato);

        SistemaLog::registrar(SistemaLog::MODULO_DATOS, SistemaLog::ACCION_DELETE, $imagen->iddato, 'Se quitó la foto ' . $imagen->dato . ' del banner');
        Yii::$app->session->setFlash('success', 'La foto se quitó del banner.');

        return $this->redirect(['banner']);
    }

    /* ------------------------------------------------------------ Destacados */

    public function actionDestacados()
    {
        return $this->render('destacados', [
            'destacados' => DatoSistema::find()->where(['idtipo' => C::TIPO_DESTACADO])->orderBy(['iddato' => SORT_DESC])->all(),
        ]);
    }

    public function actionDestacado($id = null)
    {
        $destacado = $id === null ? new DatoSistema(['idtipo' => C::TIPO_DESTACADO]) : $this->buscarDato($id, C::TIPO_DESTACADO);
        $tipo = $destacado->getImagenDestacado() !== null ? 'imagen' : 'video';
        $enlace = $destacado->getVideoYoutube() !== null ? 'https://www.youtube.com/watch?v=' . $destacado->getVideoYoutube() : '';
        $errores = [];

        if ($this->request->isPost) {
            $destacado->dato = (string) $this->request->post('titulo');
            $tipo = $this->request->post('tipo') === 'imagen' ? 'imagen' : 'video';
            $enlace = trim((string) $this->request->post('enlace'));
            $imagenAnterior = $destacado->getImagenDestacado();
            $archivo = UploadedFile::getInstanceByName('archivo');

            if ($tipo === 'video') {
                $video = DatoSistema::idYoutube($enlace);

                if ($video === null) {
                    $errores[] = 'Pegá un enlace de YouTube válido.';
                } else {
                    $destacado->observacion = 'video:' . $video;
                }
            } elseif ($archivo !== null) {
                $nombre = $this->guardarImagen($archivo, self::CARPETA_DESTACADOS, $errores);

                if ($nombre !== null) {
                    $destacado->observacion = 'imagen:' . $nombre;
                }
            } elseif ($imagenAnterior === null) {
                $errores[] = 'Elegí una foto.';
            }

            if ($errores === [] && $destacado->validate()) {
                $esNuevo = $destacado->isNewRecord;
                $destacado->save(false);

                if ($imagenAnterior !== null && $destacado->getImagenDestacado() !== $imagenAnterior) {
                    $this->borrarImagen(self::CARPETA_DESTACADOS, $imagenAnterior);
                }

                SistemaLog::registrar(SistemaLog::MODULO_DATOS, $esNuevo ? SistemaLog::ACCION_CREATE : SistemaLog::ACCION_UPDATE, $destacado->iddato, ($esNuevo ? 'Se creó' : 'Se editó') . ' el destacado ' . $destacado->dato);
                Yii::$app->session->setFlash('success', 'El destacado se guardó correctamente.');

                return $this->redirect(['destacados']);
            }

            $errores = array_merge($errores, array_values($destacado->getFirstErrors()));
        }

        return $this->render('destacado', compact('destacado', 'tipo', 'enlace', 'errores'));
    }

    public function actionDestacadoEliminar($id)
    {
        $destacado = $this->buscarDato($id, C::TIPO_DESTACADO);
        $destacado->delete();

        if ($destacado->getImagenDestacado() !== null) {
            $this->borrarImagen(self::CARPETA_DESTACADOS, $destacado->getImagenDestacado());
        }

        SistemaLog::registrar(SistemaLog::MODULO_DATOS, SistemaLog::ACCION_DELETE, $destacado->iddato, 'Se eliminó el destacado ' . $destacado->dato);
        Yii::$app->session->setFlash('success', 'El destacado se eliminó.');

        return $this->redirect(['destacados']);
    }

    /* -------------------------------------------- Listas simples de datos */

    public function actionLista($tipo)
    {
        $tipo = (int) $tipo;
        $config = $this->configLista($tipo);

        return $this->render('lista', [
            'tipo' => $tipo,
            'config' => $config,
            'datos' => DatoSistema::deTipo($tipo),
            'imagenAbout' => $tipo === C::ABOUT_PARRAFO ? DatoSistema::findOne(['idtipo' => C::ABOUT_IMAGEN]) : null,
        ]);
    }

    public function actionDato($tipo, $id = null)
    {
        $tipo = (int) $tipo;
        $config = $this->configLista($tipo);
        $dato = $id === null ? new DatoSistema(['idtipo' => $tipo]) : $this->buscarDato($id, $tipo);

        if ($this->request->isPost) {
            $dato->dato = (string) $this->request->post('dato');
            $dato->observacion = $config['extra'] !== null ? (string) $this->request->post('observacion') : null;

            if ($dato->validate()) {
                $esNuevo = $dato->isNewRecord;
                $cambios = SistemaLog::diferencias($dato, ['dato' => $config['campo'], 'observacion' => [$config['extra'] ?? 'Texto', 'texto']]);
                $dato->save(false);

                SistemaLog::registrar(
                    SistemaLog::MODULO_DATOS,
                    $esNuevo ? SistemaLog::ACCION_CREATE : SistemaLog::ACCION_UPDATE,
                    $dato->iddato,
                    $esNuevo
                        ? 'Se agregó a ' . $config['titulo'] . ': ' . mb_substr($dato->dato, 0, 80)
                        : SistemaLog::conDetalle('Se editó en ' . $config['titulo'], $cambios)
                );
                Yii::$app->session->setFlash('success', 'Se guardó correctamente.');

                return $this->redirect(['lista', 'tipo' => $tipo]);
            }
        }

        return $this->render('dato', compact('tipo', 'config', 'dato'));
    }

    public function actionDatoEliminar($id)
    {
        $dato = DatoSistema::findOne((int) $id);

        if ($dato === null || !isset(self::LISTAS[$dato->idtipo])) {
            throw new NotFoundHttpException('El dato no existe.');
        }

        $campoEnUso = [C::TIPO_LUGAR_ENTRENAMIENTO => 'idlugar', C::TIPO_TIPO_CLASE_ENTRENAMIENTO => 'idmodalidad'][$dato->idtipo] ?? null;

        if ($campoEnUso !== null && Calendario::find()->where([$campoEnUso => $dato->iddato])->exists()) {
            Yii::$app->session->setFlash('error', 'No se puede eliminar porque lo usan horarios cargados. Cambialos o quitalos primero.');
        } else {
            $dato->delete();
            SistemaLog::registrar(SistemaLog::MODULO_DATOS, SistemaLog::ACCION_DELETE, $dato->iddato, 'Se eliminó de ' . self::LISTAS[$dato->idtipo]['titulo'] . ': ' . mb_substr($dato->dato, 0, 80));
            Yii::$app->session->setFlash('success', 'Se eliminó correctamente.');
        }

        return $this->redirect(['lista', 'tipo' => $dato->idtipo]);
    }

    public function actionAboutImagen()
    {
        $archivo = UploadedFile::getInstanceByName('imagen');
        $errores = [];
        $nombre = $archivo !== null ? $this->guardarImagen($archivo, self::CARPETA_ABOUT, $errores) : null;

        if ($nombre === null) {
            Yii::$app->session->setFlash('error', $errores[0] ?? 'Elegí una foto.');

            return $this->redirect(['lista', 'tipo' => C::ABOUT_PARRAFO]);
        }

        $dato = DatoSistema::findOne(['idtipo' => C::ABOUT_IMAGEN]) ?? new DatoSistema(['idtipo' => C::ABOUT_IMAGEN]);
        $anterior = $dato->isNewRecord ? null : $dato->dato;
        $dato->dato = $nombre;
        $dato->save(false);

        if ($anterior !== null) {
            $this->borrarImagen(self::CARPETA_ABOUT, $anterior);
        }

        SistemaLog::registrar(SistemaLog::MODULO_DATOS, SistemaLog::ACCION_UPDATE, $dato->iddato, 'Se cambió la imagen de Acerca de');
        Yii::$app->session->setFlash('success', 'La imagen de Acerca de se cambió.');

        return $this->redirect(['lista', 'tipo' => C::ABOUT_PARRAFO]);
    }

    /* -------------------------------------------------------------- Horarios */

    public function actionHorarios()
    {
        return $this->render('horarios', [
            'horarios' => Calendario::find()->with(['lugar', 'modalidad', 'instructor.persona'])->orderBy(['idlugar' => SORT_ASC, 'dia' => SORT_ASC, 'horario' => SORT_ASC])->all(),
        ]);
    }

    public function actionHorario($id = null)
    {
        $horario = $id === null ? new Calendario() : $this->buscarHorario($id);

        if ($horario->load($this->request->post()) && $horario->validate()) {
            $esNuevo = $horario->isNewRecord;
            $horario->save(false);

            SistemaLog::registrar(SistemaLog::MODULO_DATOS, $esNuevo ? SistemaLog::ACCION_CREATE : SistemaLog::ACCION_UPDATE, $horario->id, ($esNuevo ? 'Se agregó' : 'Se editó') . ' el horario ' . $this->describirHorario($horario));
            Yii::$app->session->setFlash('success', 'El horario se guardó correctamente.');

            return $this->redirect(['horarios']);
        }

        return $this->render('horario', [
            'horario' => $horario,
            'lugares' => DatoSistema::opciones(C::TIPO_LUGAR_ENTRENAMIENTO),
            'modalidades' => DatoSistema::opciones(C::TIPO_TIPO_CLASE_ENTRENAMIENTO),
            'profesores' => PersonaInstructor::opciones(),
        ]);
    }

    public function actionHorarioEliminar($id)
    {
        $horario = $this->buscarHorario($id);
        $descripcion = $this->describirHorario($horario);
        $horario->delete();

        SistemaLog::registrar(SistemaLog::MODULO_DATOS, SistemaLog::ACCION_DELETE, (int) $id, 'Se eliminó el horario ' . $descripcion);
        Yii::$app->session->setFlash('success', 'El horario se eliminó.');

        return $this->redirect(['horarios']);
    }

    /* ------------------------------------------------------------- Ayudantes */

    private function configLista(int $tipo): array
    {
        if (!isset(self::LISTAS[$tipo])) {
            throw new NotFoundHttpException('Esa lista no existe.');
        }

        return self::LISTAS[$tipo];
    }

    private function buscarDato($id, int $tipo): DatoSistema
    {
        $dato = DatoSistema::findOne(['iddato' => (int) $id, 'idtipo' => $tipo]);

        if ($dato === null) {
            throw new NotFoundHttpException('El dato no existe.');
        }

        return $dato;
    }

    private function buscarHorario($id): Calendario
    {
        $horario = Calendario::findOne((int) $id);

        if ($horario === null) {
            throw new NotFoundHttpException('El horario no existe.');
        }

        return $horario;
    }

    private function describirHorario(Calendario $horario): string
    {
        return (C::DIAS[$horario->dia] ?? '') . ' ' . $horario->horario . ' en ' . ($horario->lugar->dato ?? 'lugar ' . $horario->idlugar);
    }

    /**
     * Valida y guarda una imagen subida. Devuelve el nombre del archivo, o null si no es válida.
     */
    private function guardarImagen(UploadedFile $archivo, string $carpeta, array &$errores = []): ?string
    {
        $validador = new ImageValidator(['extensions' => ['jpg', 'jpeg', 'png', 'webp'], 'maxSize' => 8 * 1024 * 1024]);

        if (!$validador->validate($archivo, $error)) {
            $errores[] = $archivo->name . ': ' . $error;

            return null;
        }

        FileHelper::createDirectory(Yii::getAlias('@webroot/' . $carpeta));
        $nombre = Yii::$app->security->generateRandomString(24) . '.' . strtolower($archivo->extension);

        if (!$archivo->saveAs(Yii::getAlias('@webroot/' . $carpeta . '/' . $nombre))) {
            throw new \RuntimeException('No se pudo guardar la imagen.');
        }

        ImagenHelper::optimizar(
            Yii::getAlias('@webroot/' . $carpeta . '/' . $nombre),
            $carpeta === self::CARPETA_BANNER ? ImagenHelper::ANCHO_BANNER : ImagenHelper::ANCHO_GENERAL
        );

        return $nombre;
    }

    private function borrarImagen(string $carpeta, string $nombre): void
    {
        if ($nombre !== '' && basename($nombre) === $nombre) {
            @unlink(Yii::getAlias('@webroot/' . $carpeta . '/' . $nombre));
        }
    }
}
