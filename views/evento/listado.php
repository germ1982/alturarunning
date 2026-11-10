<?php

/** @var yii\web\View $this */
/** @var app\models\Evento[] $eventos */
/** @var bool $puedeGestionar */

use app\models\ConstantesGlobales;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Eventos';
$this->registerCss(file_get_contents(__DIR__ . '/evento.css'));
$this->registerJs(file_get_contents(__DIR__ . '/listado.js'));
?>
<div class="evento-listado mt-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h1 class="neon-title-container mb-0"><?= Html::encode($this->title) ?></h1>
        <?php if ($puedeGestionar): ?>
            <?= Html::a('<i class="bi bi-plus-lg"></i> Nuevo evento', ['create'], ['class' => 'btn btn-outline-light btn-sm']) ?>
        <?php endif; ?>
    </div>

    <div class="mb-4">
        <label class="visually-hidden" for="eventos-filtro">Filtrar eventos</label>
        <input type="search" id="eventos-filtro" class="form-control" autocomplete="off"
            placeholder="Buscar por fecha, título, descripción, desafío o inscripto...">
    </div>

    <?php if ($eventos === []): ?>
        <p class="text-warning text-center">Todavía no hay eventos cargados.</p>
    <?php endif; ?>

    <div class="row row-cols-2 row-cols-md-3 row-cols-xl-5 g-3 mb-5">
        <?php foreach ($eventos as $evento): ?>
            <?php $portada = $evento->portada; ?>
            <div class="col" data-busqueda="<?= Html::encode($evento->textoBusqueda($puedeGestionar)) ?>">
                <div class="evento-panel evento-card">
                    <?php if ($portada !== null): ?>
                        <?= Html::img('@web/' . $portada->ruta, ['class' => 'evento-card__foto', 'alt' => $evento->titulo, 'loading' => 'lazy']) ?>
                    <?php else: ?>
                        <div class="evento-card__sin-foto"><i class="bi bi-image"></i></div>
                    <?php endif; ?>

                    <div class="evento-card__estado">
                        <?php if ($evento->estado != ConstantesGlobales::EVENTO_ACTIVO): ?>
                            <span class="badge bg-warning text-dark">Inactivo</span>
                        <?php elseif ($evento->esPasado()): ?>
                            <span class="badge bg-secondary">Realizado</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Próximo</span>
                        <?php endif; ?>
                        <?php if ($puedeGestionar && $evento->getSolicitudesPendientes() > 0): ?>
                            <span class="badge bg-warning text-dark"><?= $evento->getSolicitudesPendientes() ?> solicitudes</span>
                        <?php endif; ?>
                    </div>

                    <?php if ($puedeGestionar): ?>
                        <a href="<?= Url::to(['update', 'id' => $evento->idevento]) ?>" class="btn btn-outline-light btn-sm evento-card__editar" title="Editar <?= Html::encode($evento->titulo) ?>">
                            <i class="bi bi-pencil"></i>
                        </a>
                    <?php endif; ?>

                    <div class="evento-card__cuerpo">
                        <div class="evento-card__titulo">
                            <?= Html::a(Html::encode($evento->titulo), ['ver', 'id' => $evento->idevento], ['class' => 'stretched-link']) ?>
                        </div>
                        <small class="evento-tenue"><?= $evento->getFechaCorta() ?></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <p id="eventos-sin-resultados" class="text-warning text-center d-none">No hay eventos que coincidan con la búsqueda.</p>
</div>
