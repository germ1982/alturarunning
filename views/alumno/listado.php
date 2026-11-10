<?php

/** @var yii\web\View $this */
/** @var app\models\Alumno[] $alumnos */
/** @var app\models\Alumno|null $propio */
/** @var bool $puedeGestionar */

use app\models\ConstantesGlobales;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Alumnos';
$this->registerCss(file_get_contents(__DIR__ . '/../evento/evento.css'));
$this->registerJs(file_get_contents(__DIR__ . '/../evento/listado.js'));

$coloresEstado = [
    ConstantesGlobales::ALUMNO_ACTIVO => 'bg-success',
    ConstantesGlobales::ALUMNO_INACTIVO => 'bg-secondary',
    ConstantesGlobales::ALUMNO_SANCIONADO => 'bg-danger',
];
$idPropio = (int) ($propio->idalumno ?? 0);
?>
<div class="alumno-listado mt-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h1 class="neon-title-container mb-0"><?= Html::encode($this->title) ?></h1>
        <div class="d-flex flex-wrap gap-2">
            <?php if ($propio !== null): ?>
                <?= Html::a('<i class="bi bi-person-badge"></i> Mi ficha', ['ver', 'id' => $propio->idalumno], ['class' => 'btn btn-outline-light btn-sm']) ?>
            <?php endif; ?>
            <?php if ($puedeGestionar): ?>
                <?= Html::a('<i class="bi bi-plus-lg"></i> Nuevo alumno', ['create'], ['class' => 'btn btn-outline-light btn-sm']) ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="mb-4">
        <label class="visually-hidden" for="eventos-filtro">Filtrar alumnos</label>
        <input type="search" id="eventos-filtro" class="form-control" autocomplete="off" placeholder="Buscar por nombre o modalidad...">
    </div>

    <?php if ($alumnos === []): ?>
        <p class="text-warning text-center">Todavía no hay alumnos cargados.</p>
    <?php endif; ?>

    <div class="row row-cols-2 row-cols-md-3 row-cols-xl-5 g-3 mb-5">
        <?php foreach ($alumnos as $alumno): ?>
            <?php
            $persona = $alumno->persona;
            $foto = $persona->fotoPrincipal;
            $esPropio = (int) $alumno->idalumno === $idPropio;
            ?>
            <div class="col" data-busqueda="<?= Html::encode($alumno->textoBusqueda($puedeGestionar)) ?>">
                <div class="evento-panel evento-card">
                    <?php if ($foto !== null): ?>
                        <?= Html::img('@web/images/personas/' . $foto->ruta, ['class' => 'evento-card__foto', 'alt' => $persona->getNombreCompleto(), 'loading' => 'lazy']) ?>
                    <?php else: ?>
                        <div class="evento-card__sin-foto"><i class="bi bi-person"></i></div>
                    <?php endif; ?>

                    <div class="evento-card__estado">
                        <?php if ($puedeGestionar): ?>
                            <span class="badge <?= $coloresEstado[$alumno->estado] ?? 'bg-secondary' ?>"><?= Html::encode($alumno->getEstadoTexto()) ?></span>
                            <?php if ($alumno->getCuotasAdeudadas() > 0): ?>
                                <span class="badge bg-warning text-dark">Debe <?= $alumno->getCuotasAdeudadas() ?></span>
                            <?php endif; ?>
                        <?php elseif ($esPropio): ?>
                            <span class="badge bg-danger">Vos</span>
                        <?php endif; ?>
                    </div>

                    <?php if ($puedeGestionar): ?>
                        <a href="<?= Url::to(['update', 'id' => $alumno->idalumno]) ?>" class="btn btn-outline-light btn-sm evento-card__editar" title="Editar <?= Html::encode($persona->getNombreCompleto()) ?>">
                            <i class="bi bi-pencil"></i>
                        </a>
                    <?php endif; ?>

                    <div class="evento-card__cuerpo">
                        <div class="evento-card__titulo">
                            <?= Html::a(Html::encode($persona->getNombreCompleto()), ['ver', 'id' => $alumno->idalumno], ['class' => 'stretched-link']) ?>
                        </div>
                        <small class="evento-tenue"><?= Html::encode($alumno->getRolTexto()) ?></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <p id="eventos-sin-resultados" class="text-warning text-center d-none">No hay alumnos que coincidan con la búsqueda.</p>
</div>
