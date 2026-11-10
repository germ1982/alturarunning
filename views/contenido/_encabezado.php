<?php

/** @var yii\web\View $this */
/** @var array|null $volver ruta del botón volver (por defecto, el índice de contenidos) */
/** @var string|null $boton HTML de un botón extra a la derecha */

use yii\helpers\Html;
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="neon-title-container mb-0">
        <?= Html::a('<i class="fas fa-reply"></i>', $volver ?? ['index'], ['class' => 'text-light fs-5 me-1', 'title' => 'Volver']) ?>
        <?= Html::encode($this->title) ?>
    </h1>
    <?= $boton ?? '' ?>
</div>
