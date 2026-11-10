<?php

/** @var yii\web\View $this */
/** @var string $clave */
/** @var app\models\EventoDesafio $desafio */
/** @var app\models\EventoDesafioFoto[] $fotos */
/** @var app\models\EventoInscripcion[] $inscripciones */
/** @var array $personas */

use app\models\ConstantesGlobales;
use yii\helpers\Html;

$nombre = static fn (string $campo) => "Desafios[{$clave}][{$campo}]";
$hayErrores = $desafio->hasErrors();

foreach ($inscripciones as $inscripcion) {
    $hayErrores = $hayErrores || $inscripcion->hasErrors();
}

$abierto = $desafio->isNewRecord || $desafio->eliminar || $hayErrores;
$campoId = 'desafio-' . $clave . '-';
?>
<details class="form-bloque <?= $desafio->eliminar ? 'marcado-eliminar' : '' ?>" data-bloque <?= $abierto ? 'open' : '' ?>>
    <summary>
        <?= $desafio->titulo !== null && $desafio->titulo !== '' ? Html::encode($desafio->titulo) : 'Nuevo desafío' ?>
        <?php if (!$desafio->isNewRecord): ?>
            <span class="evento-tenue fw-normal">(<?= count($inscripciones) ?> inscriptos)</span>
        <?php endif; ?>
    </summary>

    <div class="pt-3">
        <?php foreach ($desafio->getFirstErrors() as $error): ?>
            <div class="text-danger small mb-2"><?= Html::encode($error) ?></div>
        <?php endforeach; ?>

        <?php if (!$desafio->isNewRecord): ?>
            <?= Html::hiddenInput($nombre('eliminar'), $desafio->eliminar ? '1' : '', ['data-eliminar' => true]) ?>
        <?php endif; ?>

        <div class="row g-2 mb-2">
            <div class="col-12 col-md-6">
                <label class="form-label" for="<?= $campoId ?>titulo">Título</label>
                <?= Html::textInput($nombre('titulo'), $desafio->titulo, ['class' => 'form-control', 'id' => $campoId . 'titulo', 'maxlength' => 150, 'placeholder' => '21 km']) ?>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label" for="<?= $campoId ?>dificultad">Dificultad</label>
                <?= Html::dropDownList($nombre('dificultad'), $desafio->dificultad, ConstantesGlobales::DIFICULTADES, ['class' => 'form-select', 'id' => $campoId . 'dificultad']) ?>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label" for="<?= $campoId ?>precio">Precio</label>
                <?= Html::input('number', $nombre('precio'), $desafio->precio, ['class' => 'form-control', 'id' => $campoId . 'precio', 'min' => 0, 'step' => '0.01']) ?>
            </div>
            <div class="col-12">
                <label class="form-label" for="<?= $campoId ?>descripcion">Descripción</label>
                <?= Html::textarea($nombre('descripcion'), $desafio->descripcion, ['class' => 'form-control', 'id' => $campoId . 'descripcion', 'rows' => 2]) ?>
            </div>
        </div>

        <?php if ($fotos !== []): ?>
            <div class="row row-cols-2 row-cols-md-4 row-cols-xl-6 g-3 mb-2">
                <?php foreach ($fotos as $foto): ?>
                    <?php $base = "Desafios[{$clave}][fotos][{$foto->idfoto}]"; ?>
                    <div class="col foto-editable">
                        <?= Html::img('@web/' . $foto->foto, ['alt' => 'Foto del desafío', 'loading' => 'lazy']) ?>
                        <div class="input-group input-group-sm mb-1">
                            <span class="input-group-text">Orden</span>
                            <?= Html::input('number', $base . '[orden]', $foto->orden, ['class' => 'form-control', 'min' => 0, 'aria-label' => 'Orden']) ?>
                        </div>
                        <?= Html::dropDownList($base . '[estado]', $foto->estado, ConstantesGlobales::ESTADOS_FOTO, ['class' => 'form-select form-select-sm mb-1', 'aria-label' => 'Estado de la foto']) ?>
                        <div class="form-check">
                            <?= Html::checkbox($base . '[eliminar]', $foto->eliminar, ['value' => 1, 'class' => 'form-check-input', 'id' => 'eliminar-df-' . $foto->idfoto]) ?>
                            <label class="form-check-label" for="eliminar-df-<?= $foto->idfoto ?>">Eliminar</label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <label class="form-label" for="<?= $campoId ?>fotos">Agregar fotos del desafío</label>
        <input type="file" data-comprimir="1280" id="<?= $campoId ?>fotos" name="desafio_fotos[<?= $clave ?>][]" class="form-control mb-3" accept=".jpg,.jpeg,.png,.webp" multiple>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <strong>Inscriptos</strong>
            <button type="button" class="btn btn-outline-light btn-sm" data-agregar-inscripcion="<?= $clave ?>"><i class="bi bi-plus-lg"></i> Agregar inscripto</button>
        </div>

        <div data-lista-inscripciones>
            <?php foreach ($inscripciones as $claveInscripcion => $inscripcion): ?>
                <?= $this->render('_inscripcion', [
                    'claveDesafio' => $clave,
                    'clave' => (string) $claveInscripcion,
                    'inscripcion' => $inscripcion,
                    'personas' => $personas,
                ]) ?>
            <?php endforeach; ?>
        </div>

        <div class="text-end">
            <button type="button" class="btn btn-outline-light btn-sm" data-quitar><?= $desafio->eliminar ? 'Deshacer' : 'Eliminar desafío' ?></button>
        </div>
    </div>
</details>
