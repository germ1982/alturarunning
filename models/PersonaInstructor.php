<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $idinstructor
 * @property int $idpersona
 * @property string|null $especialidad
 * @property string|null $biografia
 *
 * @property Persona $persona
 */
class PersonaInstructor extends ActiveRecord
{
    public static function tableName()
    {
        return 'persona_instructor';
    }

    public function rules()
    {
        return [
            [['especialidad', 'biografia'], 'trim'],
            [['especialidad', 'biografia'], 'default', 'value' => null],
            [['especialidad'], 'string', 'max' => 100],
            [['especialidad'], 'validarEspecialidad'],
            [['biografia'], 'string', 'max' => 5000],
        ];
    }

    public function attributeLabels()
    {
        return [
            'especialidad' => 'Especialidad',
            'biografia' => 'Biografía',
        ];
    }

    /** La especialidad sale de la lista cargada en Contenidos (se acepta la que ya tenía). */
    public function validarEspecialidad($atributo)
    {
        $valor = $this->$atributo;
        $disponibles = DatoSistema::opciones(ConstantesGlobales::TIPO_ESPECIALIDAD_INSTRUCTOR);

        if (!in_array($valor, $disponibles, true) && $valor !== $this->getOldAttribute($atributo)) {
            $this->addError($atributo, 'Elegí una especialidad de la lista.');
        }
    }

    public function getPersona()
    {
        return $this->hasOne(Persona::class, ['idpersona' => 'idpersona']);
    }

    /**
     * @return array<int, string> idinstructor => nombre del profesor
     */
    public static function opciones(): array
    {
        $opciones = [];

        foreach (self::find()->with('persona')->all() as $instructor) {
            $opciones[$instructor->idinstructor] = $instructor->persona->getNombreCompleto();
        }

        asort($opciones);

        return $opciones;
    }
}
