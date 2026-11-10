<?php

/** @var yii\web\View $this */
/** @var app\models\DatoSistema[] $destacados */

use yii\helpers\Html;

$this->title = 'Destacados';
?>
<div class="contenido-destacados mt-5 mb-5">
    <?= $this->render('_encabezado', [
        'boton' => Html::a('<i class="bi bi-plus-lg"></i> Nuevo destacado', ['destacado'], ['class' => 'btn btn-outline-light btn-sm']),
    ]) ?>
    <p class="evento-tenue mb-4">La tarjeta del home muestra los más recientes primero.</p>

    <?php if ($destacados === []): ?>
        <p class="text-warning">Todavía no hay destacados.</p>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
        <?php foreach ($destacados as $destacado): ?>
            <div class="col">
                <div class="evento-panel p-3 h-100">
                    <div class="ratio ratio-16x9 rounded overflow-hidden mb-2">
                        <?php if ($destacado->getVideoYoutube() !== null): ?>
                            <?= Html::img('https://i.ytimg.com/vi/' . $destacado->getVideoYoutube() . '/mqdefault.jpg', ['alt' => 'Video', 'style' => 'object-fit: cover;', 'loading' => 'lazy']) ?>
                        <?php else: ?>
                            <?= Html::img('@web/images/sistema/destacados/' . $destacado->getImagenDestacado(), ['alt' => 'Foto', 'style' => 'object-fit: cover;', 'loading' => 'lazy']) ?>
                        <?php endif; ?>
                    </div>
                    <strong class="d-block" style="overflow-wrap: anywhere;"><?= Html::encode($destacado->dato) ?></strong>
                    <small class="evento-tenue"><?= $destacado->getVideoYoutube() !== null ? 'Video de YouTube' : 'Foto' ?></small>
                    <div class="d-flex justify-content-end gap-2 mt-2">
                        <?= Html::a('<i class="bi bi-pencil"></i> Editar', ['destacado', 'id' => $destacado->iddato], ['class' => 'btn btn-outline-light btn-sm']) ?>
                        <?= Html::a('<i class="bi bi-trash"></i>', ['destacado-eliminar', 'id' => $destacado->iddato], [
                            'class' => 'btn btn-outline-light btn-sm',
                            'title' => 'Eliminar',
                            'data-method' => 'post',
                            'data-confirm' => '¿Eliminar el destacado ' . $destacado->dato . '?',
                        ]) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
