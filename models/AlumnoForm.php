<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\validators\ImageValidator;
use yii\web\UploadedFile;

/**
 * Carga y guarda en una sola transacción un alumno con sus datos personales,
 * su foto, sus cuotas y sus objetivos.
 *
 * Lo existente solo se borra si viene marcado; las filas nuevas usan claves "n...".
 */
class AlumnoForm extends Model
{
    private const CARPETA_FOTOS = 'images/personas';
    private const CLAVE_VALIDA = '/^n?\d+$/';

    public Alumno $alumno;
    public Persona $persona;
    public $personaExistente;
    public bool $quitarFoto = false;

    /** @var array<string, CuotaPago> */
    public array $pagos = [];
    /** @var array<string, ObjetivoDesarrollo> */
    public array $objetivos = [];

    private ?UploadedFile $archivoFoto = null;

    public function __construct(Alumno $alumno, $config = [])
    {
        $this->alumno = $alumno;
        parent::__construct($config);

        if ($alumno->isNewRecord) {
            $this->persona = new Persona();
            return;
        }

        $this->persona = $alumno->persona;

        foreach ($alumno->pagos as $pago) {
            $this->pagos[(string) $pago->idpago] = $pago;
        }

        foreach ($this->persona->objetivosDesarrollo as $desarrollo) {
            $this->objetivos[(string) $desarrollo->idobjetivo] = $desarrollo;
        }
    }

    public function cargar(array $post): void
    {
        $this->alumno->load($post);
        $this->aplicarPersona($post);
        $this->quitarFoto = !empty($post['quitar_foto']);
        $this->cargarPagos((array) ($post['Pagos'] ?? []));
        $this->cargarObjetivos((array) ($post['Objetivos'] ?? []));
        $this->archivoFoto = UploadedFile::getInstanceByName('foto_nueva');
    }

    public function guardar(): bool
    {
        if (!$this->validarTodo()) {
            return false;
        }

        $escrito = null;
        $aBorrar = [];
        $transaccion = Yii::$app->db->beginTransaction();

        try {
            if (!$this->personaExistente) {
                $this->persona->save(false);
            }

            $this->alumno->idpersona = $this->persona->idpersona;
            $this->alumno->save(false);
            $this->guardarFoto($escrito, $aBorrar);
            $this->guardarPagos();
            $this->guardarObjetivos();

            $transaccion->commit();
        } catch (\Throwable $e) {
            $transaccion->rollBack();

            if ($escrito !== null) {
                self::borrarArchivo($escrito);
            }

            throw $e;
        }

        array_walk($aBorrar, [self::class, 'borrarArchivo']);

        return true;
    }

    /**
     * @return string[]
     */
    public function erroresGenerales(): array
    {
        return $this->getErrors('general');
    }

    private function aplicarPersona(array $post): void
    {
        $idExistente = (int) ($post['persona_existente'] ?? 0);

        if ($this->alumno->isNewRecord && $idExistente > 0) {
            $persona = Persona::findOne($idExistente);

            if ($persona !== null && !Alumno::find()->where(['idpersona' => $idExistente])->exists()) {
                $this->persona = $persona;
                $this->personaExistente = $idExistente;
                return;
            }
        }

        $this->persona->load($post);
    }

    private function cargarPagos(array $filas): void
    {
        foreach ($filas as $clave => $datos) {
            $clave = (string) $clave;

            if (!preg_match(self::CLAVE_VALIDA, $clave) || !is_array($datos)) {
                continue;
            }

            if ($clave[0] === 'n') {
                $this->pagos[$clave] = new CuotaPago();
            } elseif (!isset($this->pagos[$clave])) {
                continue;
            }

            $pago = $this->pagos[$clave];
            $pago->setAttributes($datos);
            $pago->eliminar = !empty($datos['eliminar']);

            // Sin monto propio, vale el de la cuota.
            if ((string) $pago->monto === '' && $pago->idcuota) {
                $pago->monto = Cuota::find()->select('monto')->where(['idcuota' => (int) $pago->idcuota])->scalar() ?: null;
            }
        }
    }

    private function cargarObjetivos(array $filas): void
    {
        foreach ($filas as $clave => $datos) {
            $clave = (string) $clave;

            if (!preg_match(self::CLAVE_VALIDA, $clave) || !is_array($datos)) {
                continue;
            }

            if ($clave[0] === 'n') {
                $this->objetivos[$clave] = new ObjetivoDesarrollo();
                $this->objetivos[$clave]->setAttributes($datos);
            } elseif (isset($this->objetivos[$clave])) {
                $this->objetivos[$clave]->estado = $datos['estado'] ?? $this->objetivos[$clave]->estado;
            } else {
                continue;
            }

            $this->objetivos[$clave]->eliminar = !empty($datos['eliminar']);
        }
    }

    private function validarTodo(): bool
    {
        $correcto = $this->alumno->validate();

        if (!$this->personaExistente) {
            $correcto = $this->persona->validate() && $correcto;
        }

        $correcto = $this->validarFilas($this->pagos, 'idcuota', 'Esta cuota ya está cargada para el alumno.') && $correcto;
        $correcto = $this->validarFilas($this->objetivos, 'idobjetivo', 'Este objetivo ya está asignado al alumno.') && $correcto;

        if ($this->archivoFoto !== null) {
            $validador = new ImageValidator(['extensions' => ['jpg', 'jpeg', 'png', 'webp'], 'maxSize' => 8 * 1024 * 1024]);

            if (!$validador->validate($this->archivoFoto, $error)) {
                $this->addError('general', $this->archivoFoto->name . ': ' . $error);
                $correcto = false;
            }
        }

        return $correcto;
    }

    private function validarFilas(array $filas, string $campoUnico, string $mensaje): bool
    {
        $correcto = true;
        $vistos = [];

        foreach ($filas as $fila) {
            if ($fila->eliminar) {
                continue;
            }

            $correcto = $fila->validate() && $correcto;
            $valor = (int) $fila->$campoUnico;

            if (in_array($valor, $vistos, true)) {
                $fila->addError($campoUnico, $mensaje);
                $correcto = false;
            }

            $vistos[] = $valor;
        }

        return $correcto;
    }

    private function guardarFoto(?string &$escrito, array &$aBorrar): void
    {
        if ($this->archivoFoto === null && !$this->quitarFoto) {
            return;
        }

        foreach (PersonaFoto::findAll(['idpersona' => $this->persona->idpersona, 'es_principal' => 1]) as $anterior) {
            $aBorrar[] = $anterior->ruta;
            $anterior->delete();
        }

        if ($this->archivoFoto === null) {
            return;
        }

        FileHelper::createDirectory(Yii::getAlias('@webroot/' . self::CARPETA_FOTOS));
        $nombre = Yii::$app->security->generateRandomString(24) . '.' . strtolower($this->archivoFoto->extension);

        if (!$this->archivoFoto->saveAs(Yii::getAlias('@webroot/' . self::CARPETA_FOTOS . '/' . $nombre))) {
            throw new \RuntimeException('No se pudo guardar la foto.');
        }

        $escrito = $nombre;

        (new PersonaFoto([
            'idpersona' => $this->persona->idpersona,
            'ruta' => $nombre,
            'es_principal' => 1,
        ]))->save(false);
    }

    private function guardarPagos(): void
    {
        // Primero las bajas, para poder volver a cargar la misma cuota sin chocar con el índice único.
        foreach ($this->pagos as $pago) {
            if ($pago->eliminar && !$pago->isNewRecord) {
                $pago->delete();
            }
        }

        foreach ($this->pagos as $pago) {
            if (!$pago->eliminar) {
                $pago->idalumno = $this->alumno->idalumno;
                $pago->save(false);
            }
        }
    }

    private function guardarObjetivos(): void
    {
        foreach ($this->objetivos as $desarrollo) {
            if ($desarrollo->eliminar && !$desarrollo->isNewRecord) {
                $desarrollo->delete();
            }
        }

        foreach ($this->objetivos as $desarrollo) {
            if (!$desarrollo->eliminar) {
                $desarrollo->idpersona = $this->persona->idpersona;
                $desarrollo->save(false);
            }
        }
    }

    private static function borrarArchivo($nombre): void
    {
        if ($nombre !== '' && basename((string) $nombre) === $nombre) {
            @unlink(Yii::getAlias('@webroot/' . self::CARPETA_FOTOS . '/' . $nombre));
        }
    }
}
