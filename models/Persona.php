<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "persona".
 *
 * @property int $idpersona
 * @property string $nombre
 * @property string $apellido
 * @property string $documento
 * @property string|null $fecha_nacimiento
 * @property string|null $direccion
 * @property string|null $telefono
 * @property string|null $email
 *
 * @property User[] $users
 */
class Persona extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'persona';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'apellido', 'documento', 'direccion', 'telefono', 'email'], 'trim'],
            [['fecha_nacimiento', 'direccion', 'telefono', 'email'], 'default', 'value' => null],
            [['nombre', 'apellido', 'documento'], 'required'],
            [['fecha_nacimiento'], 'date', 'format' => 'php:Y-m-d', 'message' => 'La fecha no es válida.'],
            [['nombre', 'apellido'], 'string', 'max' => 100],
            [['documento'], 'string', 'max' => 20],
            [['direccion'], 'string', 'max' => 200],
            [['telefono'], 'string', 'max' => 30],
            [['email'], 'string', 'max' => 150],
            [['email'], 'email'],
            [['documento'], 'unique', 'message' => 'Este documento ya está registrado.'],
            [['email'], 'unique', 'message' => 'Este email ya está registrado.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idpersona' => 'ID Persona',
            'nombre' => 'Nombre',
            'apellido' => 'Apellido',
            'documento' => 'Documento',
            'fecha_nacimiento' => 'Fecha de Nacimiento',
            'direccion' => 'Dirección',
            'telefono' => 'Teléfono',
            'email' => 'Correo Electrónico',
        ];
    }

    /**
     * Relación con el modelo User (asumiendo que en la tabla user la FK se llama idpersona)
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['idpersona' => 'idpersona']);
    }

    public function getFotoPrincipal()
    {
        return $this->hasOne(PersonaFoto::class, ['idpersona' => 'idpersona'])
            ->andOnCondition(['es_principal' => 1]);
    }

    public function getObjetivosDesarrollo()
    {
        return $this->hasMany(ObjetivoDesarrollo::class, ['idpersona' => 'idpersona'])
            ->orderBy(['idobjetivo' => SORT_ASC]);
    }

    public function getDocumentacion()
    {
        return $this->hasMany(PersonaDocumentacion::class, ['idpersona' => 'idpersona'])
            ->with('tipo')
            ->orderBy(['iddocumentacion' => SORT_DESC]);
    }

    public function getNombreCompleto(): string
    {
        return $this->nombre . ' ' . $this->apellido;
    }
}