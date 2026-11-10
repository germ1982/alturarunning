<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $iddesafio
 * @property int $idevento
 * @property string $titulo
 * @property string|null $descripcion
 * @property int $dificultad
 * @property string $precio
 *
 * @property Evento $evento
 * @property EventoDesafioFoto[] $fotos
 * @property EventoInscripcion[] $inscripciones
 */
class EventoDesafio extends ActiveRecord
{
    /** Marcado para borrar en el formulario. */
    public bool $eliminar = false;

    public static function tableName()
    {
        return 'evento_desafio';
    }

    public function rules()
    {
        return [
            [['titulo', 'descripcion'], 'trim'],
            [['titulo', 'dificultad', 'precio'], 'required'],
            [['titulo'], 'string', 'max' => 150],
            [['descripcion'], 'string'],
            [['dificultad'], 'in', 'range' => array_keys(ConstantesGlobales::DIFICULTADES)],
            [['precio'], 'number', 'min' => 0, 'max' => 99999999],
        ];
    }

    public function attributeLabels()
    {
        return [
            'titulo' => 'Título',
            'descripcion' => 'Descripción',
            'dificultad' => 'Dificultad',
            'precio' => 'Precio',
        ];
    }

    public function getEvento()
    {
        return $this->hasOne(Evento::class, ['idevento' => 'idevento']);
    }

    public function getFotos()
    {
        return $this->hasMany(EventoDesafioFoto::class, ['iddesafio' => 'iddesafio'])
            ->orderBy(['orden' => SORT_ASC, 'idfoto' => SORT_ASC]);
    }

    public function getInscripciones()
    {
        return $this->hasMany(EventoInscripcion::class, ['iddesafio' => 'iddesafio']);
    }

    public function getPrecioTexto(): string
    {
        return self::formatearPrecio((float) $this->precio);
    }

    public static function formatearPrecio(float $precio): string
    {
        return '$ ' . number_format($precio, floor($precio) == $precio ? 0 : 2, ',', '.');
    }
}
