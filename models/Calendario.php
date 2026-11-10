<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Horario de entrenamiento: qué día, a qué hora, dónde, de qué modalidad y con qué profesor.
 *
 * @property int $id
 * @property int $idlugar dato_sistema de tipo TIPO_LUGAR_ENTRENAMIENTO
 * @property int $idmodalidad dato_sistema de tipo TIPO_TIPO_CLASE_ENTRENAMIENTO
 * @property int $idinstructor
 * @property int $dia ConstantesGlobales::DIAS
 * @property string $horario
 *
 * @property DatoSistema $lugar
 * @property DatoSistema $modalidad
 * @property PersonaInstructor $instructor
 */
class Calendario extends ActiveRecord
{
    public static function tableName()
    {
        return 'calendario';
    }

    public function rules()
    {
        return [
            [['horario'], 'trim'],
            [['idlugar', 'idmodalidad', 'idinstructor', 'dia', 'horario'], 'required'],
            [['idlugar', 'idmodalidad', 'idinstructor', 'dia'], 'integer'],
            [['idlugar'], 'exist', 'targetClass' => DatoSistema::class, 'targetAttribute' => 'iddato', 'filter' => ['idtipo' => ConstantesGlobales::TIPO_LUGAR_ENTRENAMIENTO]],
            [['idmodalidad'], 'exist', 'targetClass' => DatoSistema::class, 'targetAttribute' => 'iddato', 'filter' => ['idtipo' => ConstantesGlobales::TIPO_TIPO_CLASE_ENTRENAMIENTO]],
            [['idinstructor'], 'exist', 'targetClass' => PersonaInstructor::class, 'targetAttribute' => 'idinstructor'],
            [['dia'], 'in', 'range' => array_keys(ConstantesGlobales::DIAS)],
            [['horario'], 'string', 'max' => 20],
        ];
    }

    public function attributeLabels()
    {
        return [
            'idlugar' => 'Lugar',
            'idmodalidad' => 'Modalidad',
            'idinstructor' => 'Profesor',
            'dia' => 'Día',
            'horario' => 'Horario',
        ];
    }

    public function getLugar()
    {
        return $this->hasOne(DatoSistema::class, ['iddato' => 'idlugar']);
    }

    public function getModalidad()
    {
        return $this->hasOne(DatoSistema::class, ['iddato' => 'idmodalidad']);
    }

    public function getInstructor()
    {
        return $this->hasOne(PersonaInstructor::class, ['idinstructor' => 'idinstructor']);
    }
}
