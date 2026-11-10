<?php

/** @var yii\web\View $this */
/** @var app\models\AlumnoForm $modelo */
/** @var array $cuotas */
/** @var array $objetivos */
/** @var array $personasLibres */

use app\models\ConstantesGlobales;
use app\models\CuotaPago;
use app\models\ObjetivoDesarrollo;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->registerCss(file_get_contents(__DIR__ . '/../evento/evento.css'));
$this->registerJs(file_get_contents(__DIR__ . '/_form.js'));

$alumno = $modelo->alumno;
$persona = $modelo->persona;
$esNuevo = $alumno->isNewRecord;
$volver = $esNuevo ? ['index'] : ['ver', 'id' => $alumno->idalumno];
$fotoActual = $esNuevo ? null : $persona->fotoPrincipal;
?>
<div class="alumno-form mt-5 mb-5">
    <h1 class="neon-title-container mb-3">
        <?= Html::a('<i class="fas fa-reply"></i>', $volver, ['class' => 'text-light fs-5 me-1', 'title' => 'Volver']) ?>
        <?= Html::encode($this->title) ?>
    </h1>

    <?php foreach ($modelo->erroresGenerales() as $error): ?>
        <div class="alert alert-danger"><?= Html::encode($error) ?></div>
    <?php endforeach; ?>

    <?php $form = ActiveForm::begin([
        'id' => 'form-alumno',
        'enableClientValidation' => false,
        'options' => ['enctype' => 'multipart/form-data', 'class' => 'form-integrado'],
    ]); ?>

    <section class="evento-panel p-3 mb-4">
        <h2 class="evento-seccion">Datos del alumno</h2>
        <div class="row">
            <div class="col-6"><?= $form->field($alumno, 'rol')->dropDownList(ConstantesGlobales::ROLES_ALUMNO) ?></div>
            <div class="col-6"><?= $form->field($alumno, 'estado')->dropDownList(ConstantesGlobales::ESTADOS_ALUMNO) ?></div>
        </div>

        <?php if ($esNuevo): ?>
            <div class="mb-3">
                <label class="form-label" for="persona-existente">Persona ya registrada (opcional)</label>
                <?= Html::dropDownList('persona_existente', $modelo->personaExistente, $personasLibres, [
                    'class' => 'form-select',
                    'id' => 'persona-existente',
                    'prompt' => 'Es una persona nueva: completo sus datos',
                ]) ?>
            </div>
        <?php endif; ?>

        <div id="datos-persona" class="row <?= $modelo->personaExistente ? 'd-none' : '' ?>">
            <div class="col-12 col-md-4"><?= $form->field($persona, 'nombre')->textInput(['maxlength' => true]) ?></div>
            <div class="col-12 col-md-4"><?= $form->field($persona, 'apellido')->textInput(['maxlength' => true]) ?></div>
            <div class="col-6 col-md-4"><?= $form->field($persona, 'documento')->textInput(['maxlength' => true]) ?></div>
            <div class="col-6 col-md-4"><?= $form->field($persona, 'fecha_nacimiento')->input('date') ?></div>
            <div class="col-12 col-md-4"><?= $form->field($persona, 'telefono')->textInput(['maxlength' => true]) ?></div>
            <div class="col-12 col-md-4"><?= $form->field($persona, 'email')->textInput(['maxlength' => true]) ?></div>
            <div class="col-12"><?= $form->field($persona, 'direccion')->textInput(['maxlength' => true]) ?></div>
        </div>
    </section>

    <section class="evento-panel p-3 mb-4">
        <h2 class="evento-seccion">Foto</h2>
        <?php if ($fotoActual !== null): ?>
            <div class="d-flex align-items-center gap-3 mb-3">
                <?= Html::img('@web/images/personas/' . $fotoActual->ruta, ['class' => 'inscripto-avatar', 'alt' => 'Foto actual']) ?>
                <div class="form-check">
                    <?= Html::checkbox('quitar_foto', $modelo->quitarFoto, ['value' => 1, 'class' => 'form-check-input', 'id' => 'quitar-foto']) ?>
                    <label class="form-check-label" for="quitar-foto">Quitar foto</label>
                </div>
            </div>
        <?php endif; ?>
        <label class="form-label" for="foto-nueva"><?= $fotoActual !== null ? 'Reemplazar foto' : 'Subir foto' ?></label>
        <input type="file" id="foto-nueva" name="foto_nueva" class="form-control" accept=".jpg,.jpeg,.png,.webp">
        <div class="form-text">JPG, PNG o WEBP, hasta 8 MB.</div>
    </section>

    <section class="evento-panel p-3 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h2 class="evento-seccion mb-0">Cuotas</h2>
            <button type="button" class="btn btn-outline-light btn-sm" data-agregar="tpl-pago" data-lista="lista-pagos"><i class="bi bi-plus-lg"></i> Agregar cuota</button>
        </div>
        <?php if ($cuotas === []): ?>
            <p class="evento-tenue">Primero cargá las cuotas en <?= Html::a('Cuotas', ['cuota/index'], ['class' => 'text-decoration-underline']) ?>.</p>
        <?php endif; ?>
        <div id="lista-pagos">
            <?php foreach ($modelo->pagos as $clave => $pago): ?>
                <?= $this->render('_pago', ['clave' => (string) $clave, 'pago' => $pago, 'cuotas' => $cuotas]) ?>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="evento-panel p-3 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h2 class="evento-seccion mb-0">Objetivos</h2>
            <button type="button" class="btn btn-outline-light btn-sm" data-agregar="tpl-objetivo" data-lista="lista-objetivos"><i class="bi bi-plus-lg"></i> Agregar objetivo</button>
        </div>
        <?php if ($objetivos === []): ?>
            <p class="evento-tenue">Primero cargá los objetivos en <?= Html::a('Objetivos', ['objetivo/index'], ['class' => 'text-decoration-underline']) ?>.</p>
        <?php endif; ?>
        <div id="lista-objetivos">
            <?php foreach ($modelo->objetivos as $clave => $desarrollo): ?>
                <?= $this->render('_objetivo', ['clave' => (string) $clave, 'desarrollo' => $desarrollo, 'objetivos' => $objetivos]) ?>
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

<template id="tpl-pago">
    <?= $this->render('_pago', [
        'clave' => '__K__',
        'pago' => new CuotaPago(['estado_pago' => ConstantesGlobales::CUOTA_PENDIENTE]),
        'cuotas' => $cuotas,
    ]) ?>
</template>

<template id="tpl-objetivo">
    <?= $this->render('_objetivo', [
        'clave' => '__K__',
        'desarrollo' => new ObjetivoDesarrollo(['estado' => ConstantesGlobales::OBJETIVO_PENDIENTE]),
        'objetivos' => $objetivos,
    ]) ?>
</template>
