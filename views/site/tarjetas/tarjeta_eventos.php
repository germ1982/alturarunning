<?php

use app\components\CardWidget;
use app\models\Evento;
use yii\helpers\Html;

$eventos = Evento::proximos();

$slides = '';

foreach ($eventos as $index => $evento) {
    $portada = $evento->portada;
    $contenido = $portada !== null
        ? Html::img('@web/' . $portada->ruta, [
            'alt' => $evento->titulo,
            'style' => 'max-height: 350px; width: auto; max-width: 100%; object-fit: contain; margin: 0 auto;',
        ])
        : '<p class="text-warning py-5 mb-0">' . Html::encode($evento->titulo) . '</p>';

    $slides .= '<div class="carousel-item ' . ($index === 0 ? 'active' : '') . '">'
        . Html::a($contenido, ['evento/ver', 'id' => $evento->idevento], ['title' => $evento->titulo])
        . '</div>';
}

$controles = count($eventos) > 1 ? '
    <button class="carousel-control-prev" type="button" data-bs-target="#carruselEventos" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carruselEventos" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Siguiente</span>
    </button>' : '';
?>

<?= CardWidget::widget([
    'title' => 'Próximos Eventos',
    'content' => '<div class="text-center py-2">' . ($eventos === []
        ? '<p class="text-warning mb-1">No hay eventos próximos</p>'
        : '<div id="carruselEventos" class="carousel slide" data-bs-ride="carousel"><div class="carousel-inner">' . $slides . '</div>' . $controles . '</div>') . '</div>',
    'footer' => '<div class="text-center">' . Html::a('Ver más eventos', ['evento/listado'], ['class' => 'btn btn-outline-light btn-sm']) . '</div>',
]) ?>
