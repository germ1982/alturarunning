<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $idevento
 * @property string $titulo
 * @property string|null $descripcion
 * @property string $fecha_evento
 * @property string|null $link
 * @property string|null $contacto
 * @property int|null $cupo
 * @property int $estado
 *
 * @property EventoFoto[] $fotos
 * @property EventoDesafio[] $desafios
 */
class Evento extends ActiveRecord
{
    public static function tableName()
    {
        return 'evento';
    }

    public function rules()
    {
        return [
            [['titulo', 'fecha_evento', 'estado'], 'required'],
            [['titulo', 'descripcion', 'link', 'contacto'], 'trim'],
            [['titulo'], 'string', 'max' => 150],
            [['descripcion'], 'string'],
            [['link', 'contacto'], 'string', 'max' => 255],
            [['fecha_evento'], 'date', 'format' => 'php:Y-m-d', 'message' => 'La fecha no es válida.'],
            [['cupo'], 'default', 'value' => null],
            [['cupo'], 'integer', 'min' => 0],
            [['estado'], 'in', 'range' => array_keys(ConstantesGlobales::ESTADOS_EVENTO)],
        ];
    }

    public function attributeLabels()
    {
        return [
            'titulo' => 'Título',
            'descripcion' => 'Descripción',
            'fecha_evento' => 'Fecha',
            'link' => 'Enlace',
            'contacto' => 'Contacto',
            'cupo' => 'Cupo',
            'estado' => 'Estado',
        ];
    }

    public function getFotos()
    {
        return $this->hasMany(EventoFoto::class, ['idevento' => 'idevento'])
            ->orderBy(['es_principal' => SORT_DESC, 'idfoto' => SORT_ASC]);
    }

    public function getDesafios()
    {
        return $this->hasMany(EventoDesafio::class, ['idevento' => 'idevento'])
            ->orderBy(['iddesafio' => SORT_ASC]);
    }

    public function getPortada(): ?EventoFoto
    {
        return $this->fotos[0] ?? null;
    }

    public function getSolicitudesPendientes(): int
    {
        $total = 0;

        foreach ($this->desafios as $desafio) {
            foreach ($desafio->inscripciones as $inscripcion) {
                $total += (int) $inscripcion->estado_inscripcion === ConstantesGlobales::INSCRIPCION_SOLICITADA ? 1 : 0;
            }
        }

        return $total;
    }

    public function esPasado(): bool
    {
        return $this->fecha_evento < date('Y-m-d');
    }

    public function getFechaCorta(): string
    {
        return date('d/m/Y', strtotime($this->fecha_evento));
    }

    public function getFechaLarga(): string
    {
        $t = strtotime($this->fecha_evento);

        return date('j', $t) . ' de ' . ConstantesGlobales::MESES[(int) date('n', $t)] . ' de ' . date('Y', $t);
    }

    /**
     * Eventos activos que todavía no se hicieron, del más cercano al más lejano.
     *
     * @return self[]
     */
    public static function proximos(): array
    {
        return self::find()
            ->with('fotos')
            ->where(['estado' => ConstantesGlobales::EVENTO_ACTIVO])
            ->andWhere(['>=', 'fecha_evento', date('Y-m-d')])
            ->orderBy(['fecha_evento' => SORT_ASC])
            ->all();
    }

    /**
     * Texto sobre el que trabaja el filtro del listado.
     */
    public function textoBusqueda(bool $incluirCanceladas): string
    {
        $partes = [$this->titulo, $this->descripcion, $this->contacto, $this->getFechaCorta(), $this->fecha_evento, $this->getFechaLarga()];

        foreach ($this->desafios as $desafio) {
            $partes[] = $desafio->titulo;
            $partes[] = $desafio->descripcion;

            foreach ($desafio->inscripciones as $inscripcion) {
                if ($incluirCanceladas || in_array((int) $inscripcion->estado_inscripcion, ConstantesGlobales::INSCRIPCIONES_CONFIRMADAS, true)) {
                    $partes[] = $inscripcion->persona->getNombreCompleto();
                }
            }
        }

        return implode(' ', array_filter($partes));
    }
}
