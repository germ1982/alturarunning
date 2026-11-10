<?php

/** @var yii\web\View $this */
/** @var array $secciones [título, descripción, ícono, ruta, cantidad] */

use yii\helpers\Html;

$this->title = 'Contenidos';
?>
<div class="contenido-index mt-5 mb-5">
    <?= $this->render('_encabezado', ['volver' => ['site/panel_administrar']]) ?>
    <p class="evento-tenue mb-4">Todo lo que se ve en el sitio y se carga desde acá.</p>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-3">
        <?php foreach ($secciones as [$titulo, $descripcion, $icono, $ruta, $cantidad]): ?>
            <div class="col">
                <div class="evento-panel evento-card p-3">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi <?= $icono ?> fs-2 text-acento"></i>
                        <div style="min-width: 0;">
                            <?= Html::a(Html::encode($titulo), $ruta, ['class' => 'stretched-link fw-bold d-block']) ?>
                            <small class="evento-tenue d-block"><?= Html::encode($descripcion) ?></small>
                            <span class="badge bg-acento mt-2"><?= Html::encode($cantidad) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
