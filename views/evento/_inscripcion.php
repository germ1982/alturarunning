<?php

/** @var string $claveDesafio */
/** @var string $clave */
/** @var app\models\EventoInscripcion $inscripcion */
/** @var array $personas */

use app\models\ConstantesGlobales;
use yii\helpers\Html;

$nombre = static fn (string $campo) => "Desafios[{$claveDesafio}][inscripciones][{$clave}][{$campo}]";
?>
<div class="form-bloque <?= $inscripcion->eliminar ? 'marcado-eliminar' : '' ?>" data-bloque>
    <?php foreach ($inscripcion->getFirstErrors() as $error): ?>
        <div class="text-danger small mb-2"><?= Html::encode($error) ?></div>
    <?php endforeach; ?>

    <?php if (!$inscripcion->isNewRecord): ?>
        <?= Html::hiddenInput($nombre('eliminar'), $inscripcion->eliminar ? '1' : '', ['data-eliminar' => true]) ?>
    <?php endif; ?>

    <div class="row g-2 align-items-end">
        <div class="col-12 col-lg-4">
            <label class="form-label mb-0">Persona
                <?= Html::dropDownList($nombre('idpersona'), $inscripcion->idpersona, $personas, ['class' => 'form-select form-select-sm', 'prompt' => 'Elegí una persona...']) ?>
            </label>
        </div>
        <div class="col-6 col-lg-2">
            <label class="form-label mb-0 w-100">Inscripción
                <?= Html::dropDownList($nombre('estado_inscripcion'), $inscripcion->estado_inscripcion, ConstantesGlobales::ESTADOS_INSCRIPCION, ['class' => 'form-select form-select-sm']) ?>
            </label>
        </div>
        <div class="col-6 col-lg-2">
            <label class="form-label mb-0 w-100">Pago
                <?= Html::dropDownList($nombre('estado_pago'), $inscripcion->estado_pago, ConstantesGlobales::ESTADOS_PAGO, ['class' => 'form-select form-select-sm']) ?>
            </label>
        </div>
        <div class="col-4 col-lg-1">
            <label class="form-label mb-0 w-100">Desc. %
                <?= Html::input('number', $nombre('descuento'), $inscripcion->descuento, ['class' => 'form-control form-control-sm', 'min' => 0, 'max' => 100, 'step' => '0.01']) ?>
            </label>
        </div>
        <div class="col-3 col-lg-1">
            <label class="form-label mb-0 w-100">Puesto
                <?= Html::input('number', $nombre('puesto'), $inscripcion->puesto, ['class' => 'form-control form-control-sm', 'min' => 1]) ?>
            </label>
        </div>
        <div class="col-5 col-lg-2">
            <label class="form-label mb-0 w-100">Tiempo
                <?= Html::textInput($nombre('tiempo'), $inscripcion->tiempo, ['class' => 'form-control form-control-sm', 'placeholder' => 'hh:mm:ss']) ?>
            </label>
        </div>
        <div class="col-12 col-lg-10">
            <label class="form-label mb-0 w-100">Observación
                <?= Html::textInput($nombre('observacion'), $inscripcion->observacion, ['class' => 'form-control form-control-sm']) ?>
            </label>
        </div>
        <div class="col-12 col-lg-2 text-end">
            <button type="button" class="btn btn-outline-light btn-sm" data-quitar><?= $inscripcion->eliminar ? 'Deshacer' : 'Quitar' ?></button>
        </div>
    </div>
</div>
