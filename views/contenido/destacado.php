<?php

/** @var yii\web\View $this */
/** @var app\models\DatoSistema $destacado */
/** @var string $tipo video|imagen */
/** @var string $enlace */
/** @var string[] $errores */

use yii\helpers\Html;

$this->title = $destacado->isNewRecord ? 'Nuevo destacado' : 'Editar destacado';

$this->registerJs(<<<'JS'
document.querySelectorAll('input[name="tipo"]').forEach(function (opcion) {
    opcion.addEventListener('change', function () {
        document.getElementById('campo-video').classList.toggle('d-none', opcion.value !== 'video' || !opcion.checked);
        document.getElementById('campo-imagen').classList.toggle('d-none', opcion.value !== 'imagen' || !opcion.checked);
    });
});
JS);
?>
<div class="contenido-destacado row justify-content-center mt-5 mb-5">
    <div class="col-12 col-md-8 col-lg-6">
        <?= $this->render('_encabezado', ['volver' => ['destacados']]) ?>

        <?php foreach ($errores as $error): ?>
            <div class="alert alert-danger"><?= Html::encode($error) ?></div>
        <?php endforeach; ?>

        <div class="neon-container">
            <?= Html::beginForm('', 'post', ['enctype' => 'multipart/form-data']) ?>
                <div class="mb-3">
                    <label class="form-label w-100">Título
                        <?= Html::textInput('titulo', $destacado->dato, ['class' => 'form-control', 'maxlength' => 200, 'required' => true, 'placeholder' => 'Villa El Chocón, 28 de julio de 2024']) ?>
                    </label>
                </div>

                <div class="mb-3">
                    <span class="form-label d-block">Contenido</span>
                    <div class="form-check form-check-inline">
                        <?= Html::radio('tipo', $tipo === 'video', ['value' => 'video', 'class' => 'form-check-input', 'id' => 'tipo-video']) ?>
                        <label class="form-check-label" for="tipo-video">Video de YouTube</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <?= Html::radio('tipo', $tipo === 'imagen', ['value' => 'imagen', 'class' => 'form-check-input', 'id' => 'tipo-imagen']) ?>
                        <label class="form-check-label" for="tipo-imagen">Foto</label>
                    </div>
                </div>

                <div id="campo-video" class="mb-3 <?= $tipo === 'video' ? '' : 'd-none' ?>">
                    <label class="form-label w-100">Enlace de YouTube
                        <?= Html::textInput('enlace', $enlace, ['class' => 'form-control', 'placeholder' => 'https://www.youtube.com/watch?v=...']) ?>
                    </label>
                </div>

                <div id="campo-imagen" class="mb-3 <?= $tipo === 'imagen' ? '' : 'd-none' ?>">
                    <?php if ($destacado->getImagenDestacado() !== null): ?>
                        <?= Html::img('@web/images/sistema/destacados/' . $destacado->getImagenDestacado(), ['class' => 'w-100 rounded mb-2', 'style' => 'aspect-ratio: 16 / 9; object-fit: cover;', 'alt' => 'Foto actual']) ?>
                    <?php endif; ?>
                    <label class="form-label w-100"><?= $destacado->getImagenDestacado() !== null ? 'Reemplazar foto' : 'Foto' ?>
                        <input type="file" data-comprimir="1280" name="archivo" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                    </label>
                </div>

                <div class="d-flex justify-content-center gap-3 mt-4">
                    <?= Html::submitButton('Guardar', ['class' => 'btn btn-outline-light']) ?>
                    <?= Html::a('Cancelar', ['destacados'], ['class' => 'btn btn-outline-light']) ?>
                </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>
