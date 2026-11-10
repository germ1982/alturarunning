<?php

/** @var yii\web\View $this */
/** @var app\models\ProfesorForm $modelo */
/** @var array $especialidades */
/** @var array $personasLibres */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$profesor = $modelo->instructor;
$persona = $modelo->persona;
$esNuevo = $profesor->isNewRecord;
$fotoActual = $persona->isNewRecord ? null : $persona->fotoPrincipal;

$this->registerJs(<<<'JS'
var existente = document.getElementById('persona-existente');
if (existente) {
    existente.addEventListener('change', function () {
        document.getElementById('datos-persona').classList.toggle('d-none', existente.value !== '');
    });
}
JS);
?>
<div class="profesor-form mt-5 mb-5">
    <h1 class="neon-title-container mb-3">
        <?= Html::a('<i class="fas fa-reply"></i>', ['index'], ['class' => 'text-light fs-5 me-1', 'title' => 'Volver']) ?>
        <?= Html::encode($this->title) ?>
    </h1>

    <?php foreach ($modelo->erroresGenerales() as $error): ?>
        <div class="alert alert-danger"><?= Html::encode($error) ?></div>
    <?php endforeach; ?>

    <?php $form = ActiveForm::begin([
        'id' => 'form-profesor',
        'enableClientValidation' => false,
        'options' => ['enctype' => 'multipart/form-data', 'class' => 'form-integrado'],
    ]); ?>

    <section class="evento-panel p-3 mb-4">
        <h2 class="evento-seccion">Datos personales</h2>

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
        <h2 class="evento-seccion">Como profesor</h2>
        <?= $form->field($profesor, 'especialidad')->dropDownList($especialidades, ['prompt' => 'Elegí una especialidad...']) ?>
        <?= $form->field($profesor, 'biografia')->textarea(['rows' => 5, 'placeholder' => 'Experiencia, carreras, formación...']) ?>
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
        <input type="file" data-comprimir="600" id="foto-nueva" name="foto_nueva" class="form-control" accept=".jpg,.jpeg,.png,.webp">
        <div class="form-text">JPG, PNG o WEBP, hasta 8 MB. Se muestra en un círculo en la tarjeta de horarios.</div>
    </section>

    <div class="d-flex justify-content-center gap-3">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-outline-light']) ?>
        <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-outline-light']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
