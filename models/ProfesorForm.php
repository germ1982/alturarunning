<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Alta y edición de un profesor: sus datos personales, especialidad, biografía y foto,
 * guardados en una sola transacción.
 */
class ProfesorForm extends Model
{
    public PersonaInstructor $instructor;
    public Persona $persona;
    public $personaExistente;
    public bool $quitarFoto = false;

    /** @var string[] detalle de lo que cambió, para la auditoría */
    public array $cambios = [];

    private ?UploadedFile $archivoFoto = null;

    public function __construct(PersonaInstructor $instructor, $config = [])
    {
        $this->instructor = $instructor;
        $this->persona = $instructor->isNewRecord ? new Persona() : $instructor->persona;
        parent::__construct($config);
    }

    public function cargar(array $post): void
    {
        $this->instructor->load($post);
        $this->quitarFoto = !empty($post['quitar_foto']);
        $this->archivoFoto = UploadedFile::getInstanceByName('foto_nueva');

        $idExistente = (int) ($post['persona_existente'] ?? 0);

        if ($this->instructor->isNewRecord && $idExistente > 0) {
            $persona = Persona::findOne($idExistente);

            if ($persona !== null && !PersonaInstructor::find()->where(['idpersona' => $idExistente])->exists()) {
                $this->persona = $persona;
                $this->personaExistente = $idExistente;
                return;
            }
        }

        $this->persona->load($post);
    }

    public function guardar(): bool
    {
        $correcto = $this->instructor->validate();

        if (!$this->personaExistente) {
            $correcto = $this->persona->validate() && $correcto;
        }

        if ($this->archivoFoto !== null && !PersonaFoto::validar($this->archivoFoto, $error)) {
            $this->addError('general', $error);
            $correcto = false;
        }

        if (!$correcto) {
            return false;
        }

        $this->anotarCambios();

        $escrito = null;
        $aBorrar = [];
        $transaccion = Yii::$app->db->beginTransaction();

        try {
            if (!$this->personaExistente) {
                $this->persona->save(false);
            }

            $this->instructor->idpersona = $this->persona->idpersona;
            $this->instructor->save(false);
            PersonaFoto::reemplazarPrincipal($this->persona->idpersona, $this->archivoFoto, $this->quitarFoto, $escrito, $aBorrar);

            $transaccion->commit();
        } catch (\Throwable $e) {
            $transaccion->rollBack();

            if ($escrito !== null) {
                PersonaFoto::borrarArchivo($escrito);
            }

            throw $e;
        }

        array_walk($aBorrar, [PersonaFoto::class, 'borrarArchivo']);

        return true;
    }

    /**
     * @return string[]
     */
    public function erroresGenerales(): array
    {
        return $this->getErrors('general');
    }

    private function anotarCambios(): void
    {
        if ($this->personaExistente) {
            $this->cambios[] = 'Se tomó a una persona ya registrada';
        } elseif (!$this->persona->isNewRecord) {
            $persona = SistemaLog::diferencias($this->persona, [
                'nombre' => 'Nombre',
                'apellido' => 'Apellido',
                'documento' => 'Documento',
                'fecha_nacimiento' => 'Nacimiento',
                'telefono' => 'Teléfono',
                'email' => 'Email',
                'direccion' => 'Dirección',
            ]);

            if ($persona !== []) {
                $this->cambios[] = 'Datos personales: ' . implode('; ', $persona);
            }
        }

        $profesor = SistemaLog::diferencias($this->instructor, [
            'especialidad' => 'Especialidad',
            'biografia' => ['Biografía', 'texto'],
        ]);

        if ($profesor !== []) {
            $this->cambios[] = 'Profesor: ' . implode('; ', $profesor);
        }

        if ($this->archivoFoto !== null) {
            $this->cambios[] = 'Foto nueva';
        } elseif ($this->quitarFoto) {
            $this->cambios[] = 'Se quitó la foto';
        }
    }
}
