<?php

/** @var string $clave */
/** @var app\models\CuotaPago $pago */
/** @var array $cuotas */

use app\models\ConstantesGlobales;
use yii\helpers\Html;

$nombre = static fn (string $campo) => "Pagos[{$clave}][{$campo}]";
?>
<div class="form-bloque <?= $pago->eliminar ? 'marcado-eliminar' : '' ?>" data-bloque>
    <?php foreach ($pago->getFirstErrors() as $error): ?>
        <div class="text-danger small mb-2"><?= Html::encode($error) ?></div>
    <?php endforeach; ?>

    <?php if (!$pago->isNewRecord): ?>
        <?= Html::hiddenInput($nombre('eliminar'), $pago->eliminar ? '1' : '', ['data-eliminar' => true]) ?>
    <?php endif; ?>

    <div class="row g-2 align-items-end">
        <div class="col-12 col-lg-5">
            <label class="form-label mb-0 w-100">Cuota
                <?= Html::dropDownList($nombre('idcuota'), $pago->idcuota, $cuotas, ['class' => 'form-select form-select-sm', 'prompt' => 'Elegí una cuota...']) ?>
            </label>
        </div>
        <div class="col-6 col-lg-3">
            <label class="form-label mb-0 w-100">Monto
                <?= Html::input('number', $nombre('monto'), $pago->monto, ['class' => 'form-control form-control-sm', 'min' => 0, 'step' => '0.01', 'placeholder' => 'El de la cuota']) ?>
            </label>
        </div>
        <div class="col-6 col-lg-4">
            <label class="form-label mb-0 w-100">Estado
                <?= Html::dropDownList($nombre('estado_pago'), $pago->estado_pago, ConstantesGlobales::ESTADOS_CUOTA_PAGO, ['class' => 'form-select form-select-sm']) ?>
            </label>
        </div>
        <div class="col-12 col-lg-10">
            <label class="form-label mb-0 w-100">Observación
                <?= Html::textInput($nombre('observacion'), $pago->observacion, ['class' => 'form-control form-control-sm']) ?>
            </label>
        </div>
        <div class="col-12 col-lg-2 text-end">
            <button type="button" class="btn btn-outline-light btn-sm" data-quitar><?= $pago->eliminar ? 'Deshacer' : 'Quitar' ?></button>
        </div>
    </div>
</div>
