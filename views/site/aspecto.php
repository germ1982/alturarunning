<?php

/** @var yii\web\View $this */
/** @var string $actual */

use app\helpers\TemaHelper;
use yii\helpers\Html;

$this->title = 'Aspecto del sitio';

// Al elegir una opción, todo el sitio cambia en el momento como vista previa.
$this->registerJs(<<<'JS'
document.querySelectorAll('input[name="tema"]').forEach(function (opcion) {
    opcion.addEventListener('change', function () {
        document.documentElement.setAttribute('data-tema', opcion.value);
    });
});
JS);
?>
<div class="site-aspecto mt-5 mb-5">
    <div class="neon-container">
        <div class="neon-title-container pb-2"><?= Html::encode($this->title) ?></div>
        <p class="mb-4">Elegí un tema: la página cambia en el momento para que lo veas. Se aplica a todo el sitio recién cuando tocás <strong>Aplicar</strong>.</p>

        <?= Html::beginForm(['aspecto'], 'post') ?>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-5 g-3 mb-4">
                <?php foreach (TemaHelper::TEMAS as $clave => [$nombre, $descripcion, $color]): ?>
                    <div class="col">
                        <label class="tema-opcion">
                            <input type="radio" name="tema" value="<?= Html::encode($clave) ?>" <?= $clave === $actual ? 'checked' : '' ?>>
                            <div class="tema-muestra" data-tema="<?= Html::encode($clave) ?>">
                                <div class="tema-muestra__titulo"><?= Html::encode($nombre) ?></div>
                                <div class="tema-muestra__colores" aria-hidden="true">
                                    <span style="background: var(--acento);"></span>
                                    <span style="background: var(--acento-suave);"></span>
                                    <span style="background: var(--panel-oscuro); border: 1px solid var(--acento);"></span>
                                </div>
                                <p class="small mb-2"><?= Html::encode($descripcion) ?></p>
                                <span class="tema-muestra__boton">Botón de ejemplo</span>
                                <?php if ($clave === $actual): ?>
                                    <span class="badge bg-acento ms-1">Actual</span>
                                <?php endif; ?>
                            </div>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="d-flex justify-content-center gap-3">
                <?= Html::submitButton('<i class="bi bi-check-lg"></i> Aplicar', ['class' => 'btn btn-outline-light']) ?>
                <?= Html::a('Volver', ['panel_administrar'], ['class' => 'btn btn-outline-light']) ?>
            </div>
        <?= Html::endForm() ?>
    </div>
</div>
