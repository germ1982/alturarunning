<?php

use app\components\CardWidget;
use yii\helpers\Html;

// Consultamos los eventos activos con sus fotos principales
$eventos = (new \yii\db\Query())
    ->select(['e.idevento', 'e.titulo', 'ef.ruta'])
    ->from('evento e')
    ->innerJoin('evento_fotos ef', 'ef.idevento = e.idevento')
    ->where(['e.estado' => 1])
    ->all();
?>

<?= CardWidget::widget([
    'title' => 'Próximos Eventos',
    'content' => '
        <div class="text-center py-2">
            ' . (empty($eventos) ? '
                <p class="text-warning mb-1">No hay eventos próximos</p>
            ' : '
                <div id="carruselEventos" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        ' . implode('', array_map(function($evento, $index) {
                            $active = $index === 0 ? 'active' : '';
                            return '
                                <div class="carousel-item ' . $active . '">
                                    ' . Html::img('@web/' . $evento['ruta'], [
                                        'alt' => $evento['titulo'],
                                        'style' => 'max-height: 350px; width: auto; max-width: 100%; object-fit: contain; margin: 0 auto;'
                                    ]) . '
                                </div>';
                        }, $eventos, array_keys($eventos))) . '
                    </div>
                    
                    ' . (count($eventos) > 1 ? '
                        <button class="carousel-control-prev" type="button" data-bs-target="#carruselEventos" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carruselEventos" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Siguiente</span>
                        </button>
                    ' : '') . '
                </div>
            ') . '
        </div>',
    'footer' => '<div class="text-center">' . Html::a('Ver más eventos', ['site/eventos'], ['class' => 'btn btn-outline-light btn-sm']) . '</div>',
]) ?>