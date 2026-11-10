<?php

/** @var string $clave */
/** @var app\models\ObjetivoDesarrollo $desarrollo */
/** @var array $objetivos */

use app\models\ConstantesGlobales;
use yii\helpers\Html;

$nombre = static fn (string $campo) => "Objetivos[{$clave}][{$campo}]";
?>
<div class="form-bloque <?= $desarrollo->eliminar ? 'marcado-eliminar' : '' ?>" data-bloque>
    <?php foreach ($desarrollo->getFirstErrors() as $error): ?>
        <div class="text-danger small mb-2"><?= Html::encode($error) ?></div>
    <?php endforeach; ?>

    <?php if (!$desarrollo->isNewRecord): ?>
        <?= Html::hiddenInput($nombre('eliminar'), $desarrollo->eliminar ? '1' : '', ['data-eliminar' => true]) ?>
    <?php endif; ?>

    <div class="row g-2 align-items-end">
        <div class="col-12 col-lg-7">
            <?php if ($desarrollo->isNewRecord): ?>
                <label class="form-label mb-0 w-100">Objetivo
                    <?= Html::dropDownList($nombre('idobjetivo'), $desarrollo->idobjetivo, $objetivos, ['class' => 'form-select form-select-sm', 'prompt' => 'Elegí un objetivo...']) ?>
                </label>
            <?php else: ?>
                <span class="form-label mb-0 d-block">Objetivo</span>
                <span style="overflow-wrap: anywhere;"><?= Html::encode($objetivos[$desarrollo->idobjetivo] ?? '') ?></span>
            <?php endif; ?>
        </div>
        <div class="col-8 col-lg-3">
            <label class="form-label mb-0 w-100">Estado
                <?= Html::dropDownList($nombre('estado'), $desarrollo->estado, ConstantesGlobales::ESTADOS_OBJETIVO, ['class' => 'form-select form-select-sm']) ?>
            </label>
        </div>
        <div class="col-4 col-lg-2 text-end">
            <button type="button" class="btn btn-outline-light btn-sm" data-quitar><?= $desarrollo->eliminar ? 'Deshacer' : 'Quitar' ?></button>
        </div>
    </div>
</div>
