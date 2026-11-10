<?php

/** @var yii\web\View $this */
/** @var app\models\Evento $evento */
/** @var array<int, app\models\EventoInscripcion[]> $inscriptos */
/** @var bool $puedeGestionar */
/** @var app\models\EventoInscripcion[] $solicitudes */
/** @var array<int, app\models\EventoInscripcion> $misInscripciones por iddesafio */
/** @var string|null $motivoSinSolicitud */
/** @var int|null $lugaresDisponibles */

use app\models\ConstantesGlobales;
use app\models\EventoDesafio;
use yii\helpers\Html;

$this->title = $evento->titulo;
$this->registerCss(file_get_contents(__DIR__ . '/evento.css'));

$fotos = $evento->fotos;
$portada = $evento->portada;
$esEnlace = (bool) preg_match('#^https?://#i', (string) $evento->link);
$coloresDificultad = [
    ConstantesGlobales::DIFICULTAD_BAJA => 'bg-success',
    ConstantesGlobales::DIFICULTAD_MEDIA => 'bg-warning text-dark',
    ConstantesGlobales::DIFICULTAD_ALTA => 'bg-danger',
];
?>
<div class="evento-ver mt-5 mb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h1 class="neon-title-container mb-0">
            <?= Html::a('<i class="fas fa-reply"></i>', ['listado'], ['class' => 'text-light fs-5 me-1', 'title' => 'Volver a eventos']) ?>
            <?= Html::encode($evento->titulo) ?>
        </h1>
        <?php if ($puedeGestionar): ?>
            <?= Html::a('<i class="bi bi-pencil"></i> Editar', ['update', 'id' => $evento->idevento], ['class' => 'btn btn-outline-light btn-sm']) ?>
        <?php endif; ?>
    </div>

    <div class="evento-panel p-3 mb-4">
        <div class="row g-3">
            <div class="col-12 col-md-5">
                <?php if ($portada !== null): ?>
                    <?= Html::img('@web/' . $portada->ruta, ['class' => 'evento-portada', 'alt' => $evento->titulo]) ?>
                <?php else: ?>
                    <div class="evento-card__sin-foto rounded"><i class="bi bi-image"></i></div>
                <?php endif; ?>
            </div>
            <div class="col-12 col-md-7">
                <p class="mb-2"><i class="bi bi-calendar-event text-danger me-2"></i><?= $evento->getFechaLarga() ?>
                    <span class="badge ms-1 <?= $evento->esPasado() ? 'bg-secondary' : 'bg-danger' ?>"><?= $evento->esPasado() ? 'Realizado' : 'Próximo' ?></span>
                    <?php if ($evento->estado != ConstantesGlobales::EVENTO_ACTIVO): ?>
                        <span class="badge bg-warning text-dark">Inactivo</span>
                    <?php endif; ?>
                </p>
                <?php if ($evento->cupo !== null): ?>
                    <p class="mb-2"><i class="bi bi-people text-danger me-2"></i>Cupo: <?= (int) $evento->cupo ?>
                        <?php if (!$evento->esPasado()): ?>
                            <span class="badge ms-1 <?= $lugaresDisponibles > 0 ? 'bg-success' : 'bg-secondary' ?>">
                                <?= $lugaresDisponibles > 0 ? $lugaresDisponibles . ' lugares disponibles' : 'Cupo completo' ?>
                            </span>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
                <?php if ($evento->contacto): ?>
                    <p class="mb-2"><i class="bi bi-chat-dots text-danger me-2"></i><?= Html::encode($evento->contacto) ?></p>
                <?php endif; ?>
                <?php if ($esEnlace): ?>
                    <p class="mb-2"><i class="bi bi-link-45deg text-danger me-2"></i><?= Html::a('Más información', $evento->link, ['target' => '_blank', 'rel' => 'noopener', 'class' => 'text-decoration-underline']) ?></p>
                <?php endif; ?>
                <?php if ($evento->descripcion): ?>
                    <p class="mb-0 mt-3"><?= nl2br(Html::encode($evento->descripcion)) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (count($fotos) > 1): ?>
            <div class="row row-cols-3 row-cols-md-6 g-2 mt-2 evento-galeria">
                <?php foreach (array_slice($fotos, 1) as $foto): ?>
                    <div class="col">
                        <?= Html::a(Html::img('@web/' . $foto->ruta, ['alt' => $evento->titulo, 'loading' => 'lazy']), '@web/' . $foto->ruta, ['target' => '_blank']) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($evento->desafios !== []): ?>
        <h2 class="evento-seccion">Desafíos</h2>
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3 mb-4">
            <?php foreach ($evento->desafios as $desafio): ?>
                <?php $fotosDesafio = array_filter($desafio->fotos, static fn ($f) => $f->estado == ConstantesGlobales::FOTO_ACTIVA); ?>
                <div class="col">
                    <div class="evento-panel p-3 h-100">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <strong class="fs-5" style="overflow-wrap: anywhere;"><?= Html::encode($desafio->titulo) ?></strong>
                            <span class="badge <?= $coloresDificultad[$desafio->dificultad] ?? 'bg-secondary' ?>"><?= Html::encode(ConstantesGlobales::DIFICULTADES[$desafio->dificultad] ?? '') ?></span>
                        </div>
                        <p class="text-warning fw-bold mb-2"><?= $desafio->getPrecioTexto() ?></p>
                        <?php if ($desafio->descripcion): ?>
                            <p class="mb-2"><?= nl2br(Html::encode($desafio->descripcion)) ?></p>
                        <?php endif; ?>
                        <?php if ($fotosDesafio !== []): ?>
                            <div class="row row-cols-4 g-2 evento-galeria">
                                <?php foreach ($fotosDesafio as $foto): ?>
                                    <div class="col">
                                        <?= Html::a(Html::img('@web/' . $foto->foto, ['alt' => $desafio->titulo, 'loading' => 'lazy']), '@web/' . $foto->foto, ['target' => '_blank']) ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php $mia = $misInscripciones[$desafio->iddesafio] ?? null; ?>
                        <div class="mt-3">
                            <?php if ($mia !== null && (int) $mia->estado_inscripcion === ConstantesGlobales::INSCRIPCION_SOLICITADA): ?>
                                <?= Html::beginForm(['retirar', 'id' => $mia->idinscripcion], 'post', ['class' => 'd-flex flex-wrap align-items-center gap-2']) ?>
                                    <span class="badge bg-warning text-dark">Solicitud enviada</span>
                                    <?= Html::submitButton('Retirar solicitud', ['class' => 'btn btn-outline-light btn-sm', 'data-confirm' => '¿Retirar tu solicitud para ' . $desafio->titulo . '?']) ?>
                                <?= Html::endForm() ?>
                            <?php elseif ($mia !== null): ?>
                                <span class="badge <?= (int) $mia->estado_inscripcion === ConstantesGlobales::INSCRIPCION_CANCELADA ? 'bg-secondary' : 'bg-success' ?>">
                                    Tu inscripción: <?= Html::encode(ConstantesGlobales::ESTADOS_INSCRIPCION[$mia->estado_inscripcion] ?? '') ?>
                                </span>
                            <?php elseif ($motivoSinSolicitud === null): ?>
                                <?= Html::beginForm(['solicitar', 'iddesafio' => $desafio->iddesafio], 'post') ?>
                                    <?= Html::submitButton('<i class="bi bi-flag"></i> Solicitar inscripción', ['class' => 'btn btn-outline-light btn-sm', 'data-confirm' => '¿Solicitar inscripción en ' . $desafio->titulo . '?']) ?>
                                <?= Html::endForm() ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if ($motivoSinSolicitud !== null && !Yii::$app->user->isGuest && !$evento->esPasado() && $misInscripciones === []): ?>
            <p class="evento-tenue small mb-4"><?= Html::encode($motivoSinSolicitud) ?></p>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($puedeGestionar && $solicitudes !== []): ?>
        <h2 class="evento-seccion">Solicitudes pendientes (<?= count($solicitudes) ?>)</h2>
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3 mb-4">
            <?php foreach ($solicitudes as $solicitud): ?>
                <div class="col">
                    <div class="evento-panel p-3 h-100">
                        <strong style="overflow-wrap: anywhere;"><?= Html::encode($solicitud->persona->getNombreCompleto()) ?></strong>
                        <div class="evento-tenue small mb-2"><?= Html::encode($solicitud->desafio->titulo) ?></div>
                        <div class="d-flex gap-2">
                            <?= Html::beginForm(['aprobar', 'id' => $solicitud->idinscripcion], 'post') ?>
                                <?= Html::submitButton('<i class="bi bi-check-lg"></i> Aprobar', ['class' => 'btn btn-outline-light btn-sm']) ?>
                            <?= Html::endForm() ?>
                            <?= Html::beginForm(['rechazar', 'id' => $solicitud->idinscripcion], 'post') ?>
                                <?= Html::submitButton('<i class="bi bi-x-lg"></i> Rechazar', ['class' => 'btn btn-outline-light btn-sm', 'data-confirm' => '¿Rechazar la solicitud de ' . $solicitud->persona->getNombreCompleto() . '?']) ?>
                            <?= Html::endForm() ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h2 class="evento-seccion">Inscriptos (<?= count($inscriptos) ?>)</h2>
    <?php if ($inscriptos === []): ?>
        <p class="evento-tenue">Todavía no hay inscriptos.</p>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-3">
        <?php foreach ($inscriptos as $inscripciones): ?>
            <?php
            $persona = $inscripciones[0]->persona;
            $foto = $persona->fotoPrincipal;
            ?>
            <div class="col">
                <div class="evento-panel p-3 h-100">
                    <div class="d-flex align-items-center gap-2">
                        <?php if ($foto !== null): ?>
                            <?= Html::img('@web/images/personas/' . $foto->ruta, ['class' => 'inscripto-avatar', 'alt' => $persona->getNombreCompleto(), 'loading' => 'lazy']) ?>
                        <?php else: ?>
                            <div class="inscripto-sin-foto"><i class="bi bi-person"></i></div>
                        <?php endif; ?>
                        <strong style="overflow-wrap: anywhere;"><?= Html::encode($persona->getNombreCompleto()) ?></strong>
                    </div>

                    <?php foreach ($inscripciones as $inscripcion): ?>
                        <div class="inscripto-detalle">
                            <strong><?= Html::encode($inscripcion->desafio->titulo) ?></strong>
                            <div>Puesto: <?= $inscripcion->puesto !== null ? (int) $inscripcion->puesto : '—' ?></div>
                            <div>Tiempo: <?= $inscripcion->tiempo !== null ? Html::encode($inscripcion->tiempo) : '—' ?></div>
                            <?php if ($puedeGestionar): ?>
                                <div>Inscripción: <?= Html::encode(ConstantesGlobales::ESTADOS_INSCRIPCION[$inscripcion->estado_inscripcion] ?? '') ?></div>
                                <div>Pago: <?= Html::encode(ConstantesGlobales::ESTADOS_PAGO[$inscripcion->estado_pago] ?? '') ?></div>
                                <div>Descuento: <?= (float) $inscripcion->descuento ?>%</div>
                                <div>A pagar: <?= EventoDesafio::formatearPrecio($inscripcion->getMontoFinal()) ?></div>
                                <?php if ($inscripcion->observacion): ?>
                                    <div>Observación: <?= Html::encode($inscripcion->observacion) ?></div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
