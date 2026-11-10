<?php

/** @var yii\web\View $this */
/** @var app\models\Alumno $alumno */
/** @var app\models\EventoInscripcion[] $carreras */
/** @var app\models\CuotaPago[] $pagos */
/** @var app\models\ObjetivoDesarrollo[] $objetivos */
/** @var app\models\PersonaDocumentacion[] $documentacion */
/** @var array $tiposDocumentacion */
/** @var bool $puedeGestionar */
/** @var bool $verPrivado */

use app\models\ConstantesGlobales;
use app\models\EventoDesafio;
use yii\helpers\Html;

$persona = $alumno->persona;
$foto = $persona->fotoPrincipal;
$this->title = $persona->getNombreCompleto();

$puntos = 0;

foreach ($objetivos as $desarrollo) {
    if ((int) $desarrollo->estado === ConstantesGlobales::OBJETIVO_CUMPLIDO) {
        $puntos += (int) $desarrollo->objetivo->puntaje;
    }
}
?>
<div class="alumno-ver mt-5 mb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h1 class="neon-title-container mb-0">
            <?= Html::a('<i class="fas fa-reply"></i>', ['listado'], ['class' => 'text-light fs-5 me-1', 'title' => 'Volver a alumnos']) ?>
            <?= Html::encode($this->title) ?>
        </h1>
        <?php if ($puedeGestionar): ?>
            <?= Html::a('<i class="bi bi-pencil"></i> Editar', ['update', 'id' => $alumno->idalumno], ['class' => 'btn btn-outline-light btn-sm']) ?>
        <?php endif; ?>
    </div>

    <div class="evento-panel p-3 mb-4">
        <div class="d-flex flex-wrap align-items-center gap-3">
            <?php if ($foto !== null): ?>
                <?= Html::img('@web/images/personas/' . $foto->ruta, ['class' => 'inscripto-avatar', 'style' => 'width: 110px; height: 110px;', 'alt' => $this->title]) ?>
            <?php else: ?>
                <div class="inscripto-sin-foto" style="width: 110px; height: 110px; font-size: 3rem;"><i class="bi bi-person"></i></div>
            <?php endif; ?>
            <div style="min-width: 0;">
                <p class="mb-1"><i class="bi bi-activity text-acento me-2"></i><?= Html::encode($alumno->getRolTexto()) ?></p>
                <?php if ($verPrivado): ?>
                    <p class="mb-1"><i class="bi bi-flag text-acento me-2"></i><?= Html::encode($alumno->getEstadoTexto()) ?></p>
                    <?php if ($persona->telefono): ?>
                        <p class="mb-1"><i class="bi bi-telephone text-acento me-2"></i><?= Html::encode($persona->telefono) ?></p>
                    <?php endif; ?>
                    <?php if ($persona->email): ?>
                        <p class="mb-1" style="overflow-wrap: anywhere;"><i class="bi bi-envelope text-acento me-2"></i><?= Html::encode($persona->email) ?></p>
                    <?php endif; ?>
                    <p class="mb-0"><i class="bi bi-trophy text-acento me-2"></i><?= $puntos ?> puntos · <?= $alumno->getCuotasAdeudadas() ?> cuotas sin saldar</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <h2 class="evento-seccion">Carreras (<?= count($carreras) ?>)</h2>
    <?php if ($carreras === []): ?>
        <p class="evento-tenue mb-4">Todavía no participó en carreras.</p>
    <?php endif; ?>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3 mb-4">
        <?php foreach ($carreras as $carrera): ?>
            <?php $evento = $carrera->desafio->evento; ?>
            <div class="col">
                <div class="evento-panel p-3 h-100">
                    <strong style="overflow-wrap: anywhere;"><?= Html::a(Html::encode($evento->titulo), ['evento/ver', 'id' => $evento->idevento], ['class' => 'text-decoration-underline']) ?></strong>
                    <div class="evento-tenue small"><?= $evento->getFechaCorta() ?> · <?= Html::encode($carrera->desafio->titulo) ?></div>
                    <?php if ((int) $carrera->estado_inscripcion === ConstantesGlobales::INSCRIPCION_SOLICITADA): ?>
                        <span class="badge bg-warning text-dark mt-1">Esperando aprobación</span>
                    <?php else: ?>
                        <div class="mt-1">Puesto: <?= $carrera->puesto !== null ? (int) $carrera->puesto : '—' ?> · Tiempo: <?= $carrera->tiempo !== null ? Html::encode($carrera->tiempo) : '—' ?></div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($verPrivado): ?>
        <h2 class="evento-seccion">Objetivos</h2>
        <?php if ($objetivos === []): ?>
            <p class="evento-tenue mb-4">Todavía no tiene objetivos asignados.</p>
        <?php endif; ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3 mb-4">
            <?php foreach ($objetivos as $desarrollo): ?>
                <?php $cumplido = (int) $desarrollo->estado === ConstantesGlobales::OBJETIVO_CUMPLIDO; ?>
                <div class="col">
                    <div class="evento-panel p-3 h-100">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <span style="overflow-wrap: anywhere;"><?= Html::encode($desarrollo->objetivo->descripcion) ?></span>
                            <span class="badge <?= $cumplido ? 'bg-success' : 'bg-secondary' ?>"><?= Html::encode(ConstantesGlobales::ESTADOS_OBJETIVO[$desarrollo->estado] ?? '') ?></span>
                        </div>
                        <small class="evento-tenue"><?= (int) $desarrollo->objetivo->puntaje ?> puntos</small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <h2 class="evento-seccion" id="documentacion">Documentación</h2>
        <div class="evento-panel p-3 mb-4">
            <?php if ($documentacion === []): ?>
                <p class="evento-tenue">Todavía no hay documentación cargada.</p>
            <?php endif; ?>
            <?php foreach ($documentacion as $documento): ?>
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 py-2 border-bottom border-secondary">
                    <div style="min-width: 0;">
                        <strong><?= Html::encode($documento->tipo->descripcion) ?></strong>
                        <?php if ($documento->observacion): ?>
                            <div class="evento-tenue small" style="overflow-wrap: anywhere;"><?= Html::encode($documento->observacion) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex gap-2">
                        <?= Html::a('<i class="bi bi-eye"></i> Ver', ['documentacion/ver', 'id' => $documento->iddocumentacion], ['class' => 'btn btn-outline-light btn-sm', 'target' => '_blank', 'rel' => 'noopener']) ?>
                        <?= Html::a('<i class="bi bi-trash"></i>', ['documentacion/eliminar', 'id' => $documento->iddocumentacion], [
                            'class' => 'btn btn-outline-light btn-sm',
                            'title' => 'Eliminar',
                            'data-method' => 'post',
                            'data-confirm' => '¿Eliminar ' . $documento->tipo->descripcion . '? El archivo se borra definitivamente.',
                        ]) ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <?= Html::beginForm(['documentacion/subir', 'idalumno' => $alumno->idalumno], 'post', ['enctype' => 'multipart/form-data', 'class' => 'row g-2 align-items-end mt-2']) ?>
                <div class="col-12 col-md-3">
                    <label class="form-label mb-0 w-100">Tipo
                        <?= Html::dropDownList('PersonaDocumentacion[idtipo]', null, $tiposDocumentacion, ['class' => 'form-select form-select-sm', 'required' => true]) ?>
                    </label>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label mb-0 w-100">Archivo (PDF o imagen, hasta 10 MB)
                        <input type="file" data-comprimir="2000" data-calidad="0.88" name="archivo" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png,.webp" required>
                    </label>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label mb-0 w-100">Observación
                        <?= Html::textInput('PersonaDocumentacion[observacion]', '', ['class' => 'form-control form-control-sm', 'maxlength' => 1000, 'placeholder' => 'Vence en marzo']) ?>
                    </label>
                </div>
                <div class="col-12 col-md-2 text-end">
                    <?= Html::submitButton('<i class="bi bi-upload"></i> Subir', ['class' => 'btn btn-outline-light btn-sm']) ?>
                </div>
            <?= Html::endForm() ?>
        </div>

        <h2 class="evento-seccion">Cuotas</h2>
        <?php if ($pagos === []): ?>
            <p class="evento-tenue">Todavía no tiene cuotas cargadas.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-stark align-middle mb-0">
                    <thead>
                        <tr><th>Mes</th><th>Tipo</th><th>Monto</th><th>Estado</th><th>Observación</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pagos as $pago): ?>
                            <?php $saldado = (int) $pago->estado_pago === ConstantesGlobales::CUOTA_SALDADO; ?>
                            <tr>
                                <td class="text-nowrap"><?= Html::encode($pago->cuota->getMesTexto()) ?></td>
                                <td class="text-nowrap"><?= Html::encode($pago->cuota->getTipoTexto()) ?></td>
                                <td class="text-nowrap"><?= EventoDesafio::formatearPrecio((float) $pago->monto) ?></td>
                                <td><span class="badge <?= $saldado ? 'bg-success' : 'bg-warning text-dark' ?>"><?= Html::encode(ConstantesGlobales::ESTADOS_CUOTA_PAGO[$pago->estado_pago] ?? '') ?></span></td>
                                <td><?= Html::encode($pago->observacion) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
