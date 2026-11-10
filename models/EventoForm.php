<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\validators\ImageValidator;
use yii\web\UploadedFile;

/**
 * Carga y guarda en una sola transacción un evento con sus fotos,
 * sus desafíos, las fotos de cada desafío y sus inscripciones.
 *
 * Lo existente solo se borra si viene marcado: una fila que no llega en el
 * POST queda intacta. Las claves de las filas nuevas empiezan con "n".
 */
class EventoForm extends Model
{
    private const CARPETA_EVENTOS = 'images/eventos';
    private const CARPETA_DESAFIOS = 'images/eventos/desafios';
    private const CLAVE_VALIDA = '/^n?\d+$/';

    public Evento $evento;
    public $fotoPrincipal;

    /** @var EventoFoto[] */
    public array $fotos = [];
    /** @var array<string, EventoDesafio> */
    public array $desafios = [];
    /** @var array<string, EventoDesafioFoto[]> por clave de desafío */
    public array $fotosDesafio = [];
    /** @var array<string, array<string, EventoInscripcion>> por clave de desafío */
    public array $inscripciones = [];

    /** @var UploadedFile[] */
    private array $archivosEvento = [];
    /** @var array<string, UploadedFile[]> */
    private array $archivosDesafio = [];

    public function __construct(Evento $evento, $config = [])
    {
        $this->evento = $evento;
        parent::__construct($config);

        if ($evento->isNewRecord) {
            return;
        }

        $this->fotos = $evento->fotos;
        $this->fotoPrincipal = $evento->portada->idfoto ?? null;

        foreach ($evento->desafios as $desafio) {
            $clave = (string) $desafio->iddesafio;
            $this->desafios[$clave] = $desafio;
            $this->fotosDesafio[$clave] = $desafio->fotos;
            $this->inscripciones[$clave] = [];

            foreach ($desafio->inscripciones as $inscripcion) {
                $this->inscripciones[$clave][(string) $inscripcion->idinscripcion] = $inscripcion;
            }
        }
    }

    public function cargar(array $post): void
    {
        $this->evento->load($post);
        $this->fotoPrincipal = $post['foto_principal'] ?? $this->fotoPrincipal;

        $aEliminar = array_map('intval', (array) ($post['fotos_eliminar'] ?? []));
        foreach ($this->fotos as $foto) {
            $foto->eliminar = in_array($foto->idfoto, $aEliminar, true);
        }

        foreach ((array) ($post['Desafios'] ?? []) as $clave => $datos) {
            $clave = (string) $clave;

            if (!preg_match(self::CLAVE_VALIDA, $clave) || !is_array($datos)) {
                continue;
            }

            if ($clave[0] === 'n') {
                $this->desafios[$clave] = new EventoDesafio();
                $this->fotosDesafio[$clave] = [];
                $this->inscripciones[$clave] = [];
            } elseif (!isset($this->desafios[$clave])) {
                continue;
            }

            $desafio = $this->desafios[$clave];
            $desafio->setAttributes($datos);
            $desafio->eliminar = !empty($datos['eliminar']);

            $this->cargarFotosDesafio($clave, (array) ($datos['fotos'] ?? []));
            $this->cargarInscripciones($clave, (array) ($datos['inscripciones'] ?? []));
        }

        $this->archivosEvento = UploadedFile::getInstancesByName('fotos_nuevas');
        foreach (array_keys($this->desafios) as $clave) {
            $this->archivosDesafio[$clave] = UploadedFile::getInstancesByName("desafio_fotos[$clave]");
        }
    }

    public function guardar(): bool
    {
        if (!$this->validarTodo()) {
            return false;
        }

        $escritos = [];
        $aBorrar = [];
        $transaccion = Yii::$app->db->beginTransaction();

        try {
            $this->evento->save(false);
            $this->guardarFotosEvento($escritos, $aBorrar);

            foreach ($this->desafios as $clave => $desafio) {
                $this->guardarDesafio($clave, $desafio, $escritos, $aBorrar);
            }

            $transaccion->commit();
        } catch (\Throwable $e) {
            $transaccion->rollBack();
            array_walk($escritos, [self::class, 'borrarArchivo']);
            throw $e;
        }

        array_walk($aBorrar, [self::class, 'borrarArchivo']);

        return true;
    }

    /**
     * @return string[] errores generales, que no pertenecen a una fila
     */
    public function erroresGenerales(): array
    {
        return $this->getErrors('general');
    }

    public static function borrarArchivo($ruta): void
    {
        if (str_starts_with((string) $ruta, self::CARPETA_EVENTOS . '/') && !str_contains((string) $ruta, '..')) {
            @unlink(Yii::getAlias('@webroot/' . $ruta));
        }
    }

    private function cargarFotosDesafio(string $clave, array $datos): void
    {
        foreach ($this->fotosDesafio[$clave] as $foto) {
            $fila = $datos[$foto->idfoto] ?? null;

            if (is_array($fila)) {
                $foto->setAttributes($fila);
                $foto->eliminar = !empty($fila['eliminar']);
            }
        }
    }

    private function cargarInscripciones(string $claveDesafio, array $filas): void
    {
        foreach ($filas as $clave => $datos) {
            $clave = (string) $clave;

            if (!preg_match(self::CLAVE_VALIDA, $clave) || !is_array($datos)) {
                continue;
            }

            if ($clave[0] === 'n') {
                $this->inscripciones[$claveDesafio][$clave] = new EventoInscripcion();
            } elseif (!isset($this->inscripciones[$claveDesafio][$clave])) {
                continue;
            }

            $inscripcion = $this->inscripciones[$claveDesafio][$clave];
            $inscripcion->setAttributes($datos);
            $inscripcion->eliminar = !empty($datos['eliminar']);
        }
    }

    private function validarTodo(): bool
    {
        $correcto = $this->evento->validate();

        foreach ($this->desafios as $clave => $desafio) {
            $correcto = $this->validarDesafio($clave, $desafio) && $correcto;
        }

        foreach ($this->archivosEvento as $archivo) {
            $correcto = $this->validarArchivo($archivo) && $correcto;
        }

        foreach ($this->archivosDesafio as $archivos) {
            foreach ($archivos as $archivo) {
                $correcto = $this->validarArchivo($archivo) && $correcto;
            }
        }

        return $correcto;
    }

    private function validarDesafio(string $clave, EventoDesafio $desafio): bool
    {
        $vigentes = array_filter($this->inscripciones[$clave], static fn ($i) => !$i->eliminar);

        if ($desafio->eliminar) {
            if ($vigentes === []) {
                return true;
            }

            $desafio->addError('titulo', 'Tiene inscriptos: quitalos primero para poder eliminarlo.');
            return false;
        }

        $correcto = $desafio->validate();
        $personas = [];

        foreach ($this->fotosDesafio[$clave] as $foto) {
            $correcto = ($foto->eliminar || $foto->validate()) && $correcto;
        }

        foreach ($vigentes as $inscripcion) {
            $correcto = $inscripcion->validate() && $correcto;

            if (in_array((int) $inscripcion->idpersona, $personas, true)) {
                $inscripcion->addError('idpersona', 'Esta persona ya está inscripta en este desafío.');
                $correcto = false;
            }

            $personas[] = (int) $inscripcion->idpersona;
        }

        return $correcto;
    }

    private function validarArchivo(UploadedFile $archivo): bool
    {
        $validador = new ImageValidator([
            'extensions' => ['jpg', 'jpeg', 'png', 'webp'],
            'maxSize' => 8 * 1024 * 1024,
        ]);

        if ($validador->validate($archivo, $error)) {
            return true;
        }

        $this->addError('general', $archivo->name . ': ' . $error);

        return false;
    }

    private function guardarFotosEvento(array &$escritos, array &$aBorrar): void
    {
        $idevento = $this->evento->idevento;

        foreach ($this->fotos as $foto) {
            if ($foto->eliminar) {
                $aBorrar[] = $foto->ruta;
                $foto->delete();
            }
        }

        foreach ($this->archivosEvento as $archivo) {
            $foto = new EventoFoto(['idevento' => $idevento, 'es_principal' => 0]);
            $foto->ruta = $escritos[] = $this->guardarArchivo($archivo, self::CARPETA_EVENTOS);
            $foto->save(false);
        }

        $principal = EventoFoto::findOne(['idevento' => $idevento, 'idfoto' => (int) $this->fotoPrincipal])
            ?? EventoFoto::find()->where(['idevento' => $idevento])->orderBy(['idfoto' => SORT_ASC])->one();

        EventoFoto::updateAll(['es_principal' => 0], ['idevento' => $idevento]);

        if ($principal !== null) {
            EventoFoto::updateAll(['es_principal' => 1], ['idfoto' => $principal->idfoto]);
        }
    }

    private function guardarDesafio(string $clave, EventoDesafio $desafio, array &$escritos, array &$aBorrar): void
    {
        if ($desafio->eliminar) {
            foreach ($this->inscripciones[$clave] as $inscripcion) {
                if (!$inscripcion->isNewRecord) {
                    $inscripcion->delete();
                }
            }

            if (!$desafio->isNewRecord) {
                foreach ($this->fotosDesafio[$clave] as $foto) {
                    $aBorrar[] = $foto->foto;
                }
                $desafio->delete();
            }

            return;
        }

        $desafio->idevento = $this->evento->idevento;
        $desafio->save(false);

        foreach ($this->fotosDesafio[$clave] as $foto) {
            if ($foto->eliminar) {
                $aBorrar[] = $foto->foto;
                $foto->delete();
            } else {
                $foto->save(false);
            }
        }

        $orden = (int) EventoDesafioFoto::find()->where(['iddesafio' => $desafio->iddesafio])->max('orden');

        foreach ($this->archivosDesafio[$clave] ?? [] as $archivo) {
            $foto = new EventoDesafioFoto([
                'iddesafio' => $desafio->iddesafio,
                'orden' => ++$orden,
                'estado' => ConstantesGlobales::FOTO_ACTIVA,
            ]);
            $foto->foto = $escritos[] = $this->guardarArchivo($archivo, self::CARPETA_DESAFIOS);
            $foto->save(false);
        }

        // Primero las bajas, para poder volver a anotar a la misma persona sin chocar con el índice único.
        foreach ($this->inscripciones[$clave] as $inscripcion) {
            if ($inscripcion->eliminar && !$inscripcion->isNewRecord) {
                $inscripcion->delete();
            }
        }

        foreach ($this->inscripciones[$clave] as $inscripcion) {
            if (!$inscripcion->eliminar) {
                $inscripcion->iddesafio = $desafio->iddesafio;
                $inscripcion->save(false);
            }
        }
    }

    private function guardarArchivo(UploadedFile $archivo, string $carpeta): string
    {
        FileHelper::createDirectory(Yii::getAlias('@webroot/' . $carpeta));

        $ruta = $carpeta . '/' . Yii::$app->security->generateRandomString(24) . '.' . strtolower($archivo->extension);

        if (!$archivo->saveAs(Yii::getAlias('@webroot/' . $ruta))) {
            throw new \RuntimeException('No se pudo guardar el archivo ' . $archivo->name);
        }

        return $ruta;
    }
}
