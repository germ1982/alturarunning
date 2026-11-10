<?php

/** @var yii\web\View $this */
/** @var app\models\EventoForm $modelo */
/** @var array $personas */

use app\models\ConstantesGlobales;
use app\models\EventoDesafio;
use app\models\EventoInscripcion;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->registerJs(file_get_contents(__DIR__ . '/_form.js'));

$evento = $modelo->evento;
$volver = $evento->isNewRecord ? ['listado'] : ['ver', 'id' => $evento->idevento];
?>
<div class="evento-form mt-5 mb-5">
    <h1 class="neon-title-container mb-3">
        <?= Html::a('<i class="fas fa-reply"></i>', $volver, ['class' => 'text-light fs-5 me-1', 'title' => 'Volver']) ?>
        <?= Html::encode($this->title) ?>
    </h1>

    <?php foreach ($modelo->erroresGenerales() as $error): ?>
        <div class="alert alert-danger"><?= Html::encode($error) ?></div>
    <?php endforeach; ?>

    <?php $form = ActiveForm::begin([
        'id' => 'form-evento',
        'options' => ['enctype' => 'multipart/form-data', 'class' => 'form-integrado'],
    ]); ?>

    <section class="evento-panel p-3 mb-4">
        <h2 class="evento-seccion">Datos del evento</h2>
        <div class="row">
            <div class="col-12 col-md-6"><?= $form->field($evento, 'titulo')->textInput(['maxlength' => true]) ?></div>
            <div class="col-6 col-md-3"><?= $form->field($evento, 'fecha_evento')->input('date') ?></div>
            <div class="col-6 col-md-3"><?= $form->field($evento, 'estado')->dropDownList(ConstantesGlobales::ESTADOS_EVENTO) ?></div>
            <div class="col-6 col-md-2"><?= $form->field($evento, 'cupo')->input('number', ['min' => 0]) ?></div>
            <div class="col-6 col-md-5"><?= $form->field($evento, 'contacto')->textInput(['maxlength' => true]) ?></div>
            <div class="col-12 col-md-5"><?= $form->field($evento, 'link')->textInput(['maxlength' => true, 'placeholder' => 'https://...']) ?></div>
            <div class="col-12"><?= $form->field($evento, 'descripcion')->textarea(['rows' => 4]) ?></div>
        </div>
    </section>

    <section class="evento-panel p-3 mb-4">
        <h2 class="evento-seccion">Fotos del evento</h2>
        <?php if ($modelo->fotos !== []): ?>
            <div class="row row-cols-2 row-cols-md-4 row-cols-xl-6 g-3 mb-3">
                <?php foreach ($modelo->fotos as $foto): ?>
                    <div class="col foto-editable">
                        <?= Html::img('@web/' . $foto->ruta, ['alt' => 'Foto del evento', 'loading' => 'lazy']) ?>
                        <div class="form-check">
                            <?= Html::radio('foto_principal', (int) $modelo->fotoPrincipal === (int) $foto->idfoto, ['value' => $foto->idfoto, 'class' => 'form-check-input', 'id' => 'principal-' . $foto->idfoto]) ?>
                            <label class="form-check-label" for="principal-<?= $foto->idfoto ?>">Principal</label>
                        </div>
                        <div class="form-check">
                            <?= Html::checkbox('fotos_eliminar[]', $foto->eliminar, ['value' => $foto->idfoto, 'class' => 'form-check-input', 'id' => 'eliminar-' . $foto->idfoto]) ?>
                            <label class="form-check-label" for="eliminar-<?= $foto->idfoto ?>">Eliminar</label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <label class="form-label" for="fotos-nuevas">Agregar fotos</label>
        <input type="file" id="fotos-nuevas" name="fotos_nuevas[]" class="form-control" accept=".jpg,.jpeg,.png,.webp" multiple>
        <div class="form-text">JPG, PNG o WEBP, hasta 8 MB cada una. Si no hay principal, se usa la primera.</div>
    </section>

    <section class="evento-panel p-3 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h2 class="evento-seccion mb-0">Desafíos e inscriptos</h2>
            <button type="button" class="btn btn-outline-light btn-sm" id="agregar-desafio"><i class="bi bi-plus-lg"></i> Agregar desafío</button>
        </div>
        <div id="lista-desafios">
            <?php foreach ($modelo->desafios as $clave => $desafio): ?>
                <?= $this->render('_desafio', [
                    'clave' => (string) $clave,
                    'desafio' => $desafio,
                    'fotos' => $modelo->fotosDesafio[$clave],
                    'inscripciones' => $modelo->inscripciones[$clave],
                    'personas' => $personas,
                ]) ?>
            <?php endforeach; ?>
        </div>
    </section>

    <div class="d-flex justify-content-center gap-3">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-outline-light']) ?>
        <?= Html::a('Cancelar', $volver, ['class' => 'btn btn-outline-light']) ?>
    </div>

    <?= Html::hiddenInput('formulario_completo', '1') ?>

    <?php ActiveForm::end(); ?>
</div>

<template id="tpl-desafio">
    <?= $this->render('_desafio', [
        'clave' => '__D__',
        'desafio' => new EventoDesafio(['dificultad' => ConstantesGlobales::DIFICULTAD_MEDIA]),
        'fotos' => [],
        'inscripciones' => [],
        'personas' => $personas,
    ]) ?>
</template>

<template id="tpl-inscripcion">
    <?= $this->render('_inscripcion', [
        'claveDesafio' => '__D__',
        'clave' => '__I__',
        'inscripcion' => new EventoInscripcion([
            'estado_pago' => ConstantesGlobales::PAGO_PENDIENTE,
            'estado_inscripcion' => ConstantesGlobales::INSCRIPCION_ACTIVA,
            'descuento' => 0,
        ]),
        'personas' => $personas,
    ]) ?>
</template>
