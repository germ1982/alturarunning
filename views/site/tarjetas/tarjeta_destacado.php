<?php

use app\components\CardWidget;
use app\models\ConstantesGlobales;
use app\models\DatoSistema;
use yii\helpers\Html;

// Se cargan en Administrar > Contenidos > Destacados; primero los más recientes.
$destacados = DatoSistema::find()
    ->where(['idtipo' => ConstantesGlobales::TIPO_DESTACADO])
    ->orderBy(['iddato' => SORT_DESC])
    ->limit(5)
    ->all();

$marco = 'border: 1px solid rgba(var(--acento-rgb), 0.4); box-shadow: 0 0 10px rgba(var(--acento-rgb), 0.3);';
$slides = '';

foreach ($destacados as $indice => $destacado) {
    $video = $destacado->getVideoYoutube();

    if ($video !== null) {
        // Con un solo destacado el video arranca solo y en silencio, como antes.
        $parametros = count($destacados) === 1
            ? '?autoplay=1&mute=1&controls=0&loop=1&playlist=' . $video
            : '?mute=1&rel=0';
        $medio = '<iframe src="https://www.youtube.com/embed/' . $video . $parametros . '" title="' . Html::encode($destacado->dato) . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
    } else {
        $medio = Html::img('@web/images/sistema/destacados/' . $destacado->getImagenDestacado(), ['alt' => $destacado->dato, 'style' => 'object-fit: cover;', 'loading' => 'lazy']);
    }

    $slides .= '<div class="carousel-item' . ($indice === 0 ? ' active' : '') . '">'
        . '<p class="mt-0 mt-md-5">' . Html::encode($destacado->dato) . '</p>'
        . '<div class="ratio ratio-16x9 rounded overflow-hidden" style="' . $marco . '">' . $medio . '</div>'
        . '</div>';
}

$controles = count($destacados) > 1 ? '
    <div class="d-flex justify-content-center gap-3 mt-2">
        <button class="btn btn-outline-light btn-sm" type="button" data-bs-target="#carruselDestacados" data-bs-slide="prev" aria-label="Anterior"><i class="bi bi-chevron-left"></i></button>
        <button class="btn btn-outline-light btn-sm" type="button" data-bs-target="#carruselDestacados" data-bs-slide="next" aria-label="Siguiente"><i class="bi bi-chevron-right"></i></button>
    </div>' : '';
?>
<?= CardWidget::widget([
    'title' => 'Destacados',
    'content' => $destacados === []
        ? '<p class="text-warning text-center py-4 mb-0">Pronto vas a ver novedades acá.</p>'
        : '<div id="carruselDestacados" class="carousel slide" data-bs-ride="false"><div class="carousel-inner">' . $slides . '</div>' . $controles . '</div>',
    'footer' => false,
]) ?>
