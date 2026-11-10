<?php

/** @var yii\web\View $this */
/** @var app\models\PersonaInstructor[] $profesores */
/** @var array<int, string> $horarios cantidad de horarios por idinstructor */

use yii\helpers\Html;

$this->title = 'Profesores';
?>
<div class="profesor-index mt-5 mb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h1 class="neon-title-container mb-0">
            <?= Html::a('<i class="fas fa-reply"></i>', ['site/panel_administrar'], ['class' => 'text-light fs-5 me-1', 'title' => 'Volver']) ?>
            <?= Html::encode($this->title) ?>
        </h1>
        <?= Html::a('<i class="bi bi-plus-lg"></i> Nuevo profesor', ['create'], ['class' => 'btn btn-outline-light btn-sm']) ?>
    </div>
    <p class="evento-tenue mb-4">Aparecen en la tarjeta de horarios del home. Las especialidades se cargan en Contenidos.</p>

    <?php if ($profesores === []): ?>
        <p class="text-warning">Todavía no hay profesores cargados.</p>
    <?php endif; ?>

    <div class="row row-cols-2 row-cols-md-3 row-cols-xl-5 g-3">
        <?php foreach ($profesores as $profesor): ?>
            <?php
            $persona = $profesor->persona;
            $foto = $persona->fotoPrincipal;
            $cantidad = (int) ($horarios[$profesor->idinstructor] ?? 0);
            ?>
            <div class="col">
                <div class="evento-panel evento-card">
                    <?php if ($foto !== null): ?>
                        <?= Html::img('@web/images/personas/' . $foto->ruta, ['class' => 'evento-card__foto', 'alt' => $persona->getNombreCompleto(), 'loading' => 'lazy']) ?>
                    <?php else: ?>
                        <div class="evento-card__sin-foto"><i class="bi bi-person"></i></div>
                    <?php endif; ?>

                    <div class="evento-card__cuerpo">
                        <div class="evento-card__titulo"><?= Html::encode($persona->getNombreCompleto()) ?></div>
                        <small class="evento-tenue d-block"><?= Html::encode($profesor->especialidad ?? 'Sin especialidad') ?></small>
                        <small class="evento-tenue d-block"><?= $cantidad ?> <?= $cantidad === 1 ? 'horario' : 'horarios' ?></small>
                        <div class="d-flex justify-content-end gap-2 mt-2">
                            <?= Html::a('<i class="bi bi-pencil"></i>', ['update', 'id' => $profesor->idinstructor], ['class' => 'btn btn-outline-light btn-sm', 'title' => 'Editar']) ?>
                            <?= Html::a('<i class="bi bi-trash"></i>', ['delete', 'id' => $profesor->idinstructor], [
                                'class' => 'btn btn-outline-light btn-sm',
                                'title' => 'Eliminar',
                                'data-method' => 'post',
                                'data-confirm' => '¿Quitar a ' . $persona->getNombreCompleto() . ' de los profesores? Sus datos personales se conservan.',
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
