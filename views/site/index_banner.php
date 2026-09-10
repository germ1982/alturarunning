<?php

use yii\helpers\Html;
?>

<style>
    <?php include __DIR__ . '/index_banner.css'; ?>
</style>

<div class="diagonal-split-banner position-relative overflow-hidden">
    <canvas id="Rayos" class="position-absolute top-0 start-0 w-100 h-100" style="z-index: 2; pointer-events: none;"></canvas>
    
    <div id="bannerCarousel" class="carousel slide carousel-fade h-100 position-relative" data-bs-ride="carousel" data-bs-interval="5000" style="z-index: 1;">
        <div class="carousel-inner h-100">
            <div class="carousel-item active h-100">
                <?= Html::img('@web/images/sistema/carrusel_banner/imagen1.jpg', [
                    'alt' => 'Imagen 1',
                    'class' => 'd-block w-100'
                ]) ?>
            </div>
            <div class="carousel-item h-100">
                <?= Html::img('@web/images/sistema/carrusel_banner/imagen2.jpg', [
                    'alt' => 'Imagen 2',
                    'class' => 'd-block w-100'
                ]) ?>
            </div>
            <div class="carousel-item h-100">
                <?= Html::img('@web/images/sistema/carrusel_banner/imagen3.jpg', [
                    'alt' => 'Imagen 3',
                    'class' => 'd-block w-100'
                ]) ?>
            </div>
        </div>
    </div>
</div>

<script>
    <?php include __DIR__ . '/index_banner.js'; ?>
    document.addEventListener("DOMContentLoaded", function() {
        var c = document.getElementById('Rayos');
        if (c && typeof canvasLightning === 'function') {
            c.width = c.parentElement.clientWidth;
            c.height = c.parentElement.clientHeight;
            var cl = new canvasLightning(c, c.width, c.height);
            cl.init();
        }
    });
</script>