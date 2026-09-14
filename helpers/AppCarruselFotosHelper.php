<?php

namespace app\helpers;

use yii\helpers\Html;
use yii\web\View;

class AppCarruselFotosHelper
{
    private static bool $assetsRegistrados = false;

    public static function render(
        $fotos,
        $carouselId,
        $atributo = 'url',
        View $view = null
    ) {
        if (empty($fotos)) {
            return '';
        }

        if ($view !== null && !self::$assetsRegistrados) {
            self::registrarAssets($view);
            self::$assetsRegistrados = true;
        }

        ob_start();
?>

        <div class="app-carrusel-fotos">

            <div id="<?= $carouselId ?>"
                class="carousel slide"
                data-bs-ride="false">

                <div class="carousel-inner">

                    <?php foreach ($fotos as $i => $foto): ?>

                        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">

                            <img src="<?= Html::encode($foto->$atributo) ?>"
                                alt="Foto">

                        </div>

                    <?php endforeach; ?>

                </div>

                <?php if (count($fotos) > 1): ?>

                    <button class="carousel-control-prev"
                        type="button"
                        data-bs-target="#<?= $carouselId ?>"
                        data-bs-slide="prev">

                        <span class="carousel-control-prev-icon custom-carousel-control-icon"></span>

                    </button>

                    <button class="carousel-control-next"
                        type="button"
                        data-bs-target="#<?= $carouselId ?>"
                        data-bs-slide="next">

                        <span class="carousel-control-next-icon custom-carousel-control-icon"></span>

                    </button>

                <?php endif; ?>

            </div>

        </div>

<?php
        return ob_get_clean();
    }

    private static function registrarAssets(View $view)
    {
        $css = <<<CSS

.app-carrusel-fotos{
    position: relative;
    height: 100%;
    overflow: hidden;
    background: #f8f9fa;
    padding: 5px;
}

.app-carrusel-fotos img{
    width:100%;
    height:100%;
    object-fit:cover;
    display:block;
}

.app-carrusel-fotos .carousel,
.app-carrusel-fotos .carousel-inner,
.app-carrusel-fotos .carousel-item{
    height:100%;
}

/* --- ESTA ES LA PARTE QUE MODIFICAMOS --- */

/* 1. Ocultamos los botones por defecto */
.app-carrusel-fotos .carousel-control-prev,
.app-carrusel-fotos .carousel-control-next {
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
    width: 40px;
}

/* 2. Los mostramos cuando el ratón entra en el contenedor principal */
.app-carrusel-fotos:hover .carousel-control-prev,
.app-carrusel-fotos:hover .carousel-control-next {
    opacity: 1;
    visibility: visible;
}

.custom-carousel-control-icon{
    border-radius:50%;
    background-size:50%;
    background-color: 
    color-mix(in srgb, var(--grad-2), transparent 50%) !important;
    opacity: 0.7;
}

/* ---------------------------------------- */

@media (max-width:576px){
    .app-carrusel-fotos{
        height:120px;
        padding:2px;
    }
    .app-carrusel-fotos .carousel-control-prev,
    .app-carrusel-fotos .carousel-control-next{
        width:30px;
        height:30px;
        top:50%;
        transform:translateY(-50%);
    }
    .app-carrusel-fotos .carousel-control-prev-icon,
    .app-carrusel-fotos .carousel-control-next-icon{
        width:16px;
        height:16px;
    }
}

CSS;

        $js = <<<JS

$(document).on(
    'click',
    '.carousel-control-prev, .carousel-control-next',
    function(e){
        e.stopPropagation();
    }
);

JS;

        $view->registerCss($css);
        $view->registerJs($js);
    }
}
