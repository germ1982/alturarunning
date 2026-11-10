<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property int $idalumno
 * @property int $idpersona
 * @property int $rol
 * @property int $estado
 *
 * @property Persona $persona
 * @property CuotaPago[] $pagos
 */
class Alumno extends ActiveRecord
{
    public static function tableName()
    {
        return 'persona_alumno';
    }

    public function rules()
    {
        return [
            [['rol', 'estado'], 'required'],
            [['rol'], 'in', 'range' => array_keys(ConstantesGlobales::ROLES_ALUMNO)],
            [['estado'], 'in', 'range' => array_keys(ConstantesGlobales::ESTADOS_ALUMNO)],
        ];
    }

    public function attributeLabels()
    {
        return [
            'rol' => 'Modalidad',
            'estado' => 'Estado',
        ];
    }

    public function getPersona()
    {
        return $this->hasOne(Persona::class, ['idpersona' => 'idpersona']);
    }

    public function getPagos()
    {
        return $this->hasMany(CuotaPago::class, ['idalumno' => 'idalumno'])
            ->orderBy(['idpago' => SORT_DESC]);
    }

    public function getRolTexto(): string
    {
        return ConstantesGlobales::ROLES_ALUMNO[$this->rol] ?? '';
    }

    public function getEstadoTexto(): string
    {
        return ConstantesGlobales::ESTADOS_ALUMNO[$this->estado] ?? '';
    }

    public function getEstaActivo(): bool
    {
        return (int) $this->estado === ConstantesGlobales::ALUMNO_ACTIVO;
    }

    /** Cantidad de cuotas que todavía no están saldadas. */
    public function getCuotasAdeudadas(): int
    {
        return count(array_filter($this->pagos, static fn ($pago) => (int) $pago->estado_pago !== ConstantesGlobales::CUOTA_SALDADO));
    }

    public function textoBusqueda(bool $incluirPrivado): string
    {
        $partes = [$this->persona->getNombreCompleto(), $this->getRolTexto()];

        if ($incluirPrivado) {
            $partes[] = $this->persona->documento;
            $partes[] = $this->persona->email;
            $partes[] = $this->getEstadoTexto();
        }

        return implode(' ', array_filter($partes));
    }

    /** El alumno que corresponde al usuario logueado, si lo hay. */
    public static function delUsuarioActual(): ?self
    {
        $identidad = Yii::$app->user->identity;

        if ($identidad === null || empty($identidad->idpersona)) {
            return null;
        }

        return self::findOne(['idpersona' => (int) $identidad->idpersona]);
    }
}
