<?php

use app\models\ConstantesGlobales;
use yii\helpers\Html;

$imagenes = (new \yii\db\Query())
    ->select(['ds.dato'])
    ->from('dato_sistema ds')
    ->where(['ds.idtipo' => ConstantesGlobales::TIPO_IMAGEN_BANNER])
    ->column();
?>

<style>
    <?php include __DIR__ . '/tarjeta_banner.css'; ?>
</style>

<div class="diagonal-split-banner position-relative overflow-hidden">
    <canvas id="Rayos" class="position-absolute top-0 start-0 w-100 h-100" style="z-index: 2; pointer-events: none;"></canvas>

    <div id="bannerCarousel" class="carousel slide carousel-fade h-100 position-relative" data-bs-ride="carousel" data-bs-interval="5000" style="z-index: 1;">
        <div class="carousel-inner h-100">

            <?php foreach ($imagenes as $index => $ruta): ?>
                <div class="carousel-item h-100 <?= $index === 0 ? 'active' : '' ?>">
                    <?= Html::img('@web/images/sistema/carrusel_banner/' . $ruta, [
                        'alt' => 'Imagen ' . ($index + 1),
                        'class' => 'd-block w-100'
                    ]) ?>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
</div>

<script>
    <?php include __DIR__ . '/tarjeta_banner.js'; ?>
</script>