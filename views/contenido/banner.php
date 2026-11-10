<?php

/** @var yii\web\View $this */
/** @var app\models\DatoSistema[] $imagenes */

use yii\helpers\Html;

$this->title = 'Banner del home';
?>
<div class="contenido-banner mt-5 mb-5">
    <?= $this->render('_encabezado') ?>

    <div class="evento-panel p-3 mb-4">
        <?= Html::beginForm(['banner'], 'post', ['enctype' => 'multipart/form-data', 'class' => 'row g-2 align-items-end']) ?>
            <div class="col-12 col-md-9">
                <label class="form-label mb-0 w-100">Agregar fotos
                    <input type="file" data-comprimir="1920" name="imagenes[]" class="form-control" accept=".jpg,.jpeg,.png,.webp" multiple required>
                </label>
                <div class="form-text evento-tenue">JPG, PNG o WEBP, hasta 8 MB. Quedan mejor las fotos apaisadas (más o menos el doble de anchas que de altas).</div>
            </div>
            <div class="col-12 col-md-3 text-end">
                <?= Html::submitButton('<i class="bi bi-upload"></i> Subir', ['class' => 'btn btn-outline-light']) ?>
            </div>
        <?= Html::endForm() ?>
    </div>

    <?php if ($imagenes === []): ?>
        <p class="text-warning">El banner no tiene fotos.</p>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
        <?php foreach ($imagenes as $imagen): ?>
            <div class="col">
                <div class="evento-panel p-2 h-100">
                    <?= Html::img('@web/images/sistema/carrusel_banner/' . $imagen->dato, ['class' => 'w-100 rounded', 'style' => 'aspect-ratio: 2.2 / 1; object-fit: cover;', 'alt' => 'Foto del banner', 'loading' => 'lazy']) ?>
                    <div class="text-end mt-2">
                        <?= Html::a('<i class="bi bi-trash"></i> Quitar', ['banner-eliminar', 'id' => $imagen->iddato], [
                            'class' => 'btn btn-outline-light btn-sm',
                            'data-method' => 'post',
                            'data-confirm' => '¿Quitar esta foto del banner? El archivo se borra.',
                        ]) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
