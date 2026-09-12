<?php

/** @var yii\web\View $this */

use app\components\CardWidget;
use yii\helpers\Html;

$this->title = 'My Yii Application';
$this->params['meta_description'] = 'A high-performance PHP framework best for developing web applications. Fast, secure, and professional.';
$this->params['meta_keywords'] = 'yii, yii2, php, framework, web application, high-performance';
?>
<div class="site-index">

    <!-- Incluimos el banner diagonal partido con transparencia glassmorphism -->
    <div class="row g-3 mb-3">
        <div class="col-12">
            <?= $this->render('index_banner') ?>
        </div>
    </div>

    <div class="row g-3 mb-5">

        <div class="col-12 col-md-4">
            <?= $this->render('tarjetas/tarjeta_destacado') ?>
        </div>

        <div class="col-12 col-md-4">
            <?= $this->render('tarjetas/tarjeta_eventos') ?>
        </div>

        <div class="col-12 col-md-4">
            <?= $this->render('tarjetas/tarjeta_dino_motivador') ?>
        </div>

    </div>

    <div class="row g-3 ">
        <div class="col-lg-3 col-md-1 d-none d-lg-block">
            <?= Html::img('@web/images/sistema/dinofcaminando1.png', [
                'alt' => 'Entrenamiento',
                'style' => 'max-width: 326px; height: auto; object-fit: cover; text-align: left;'
            ]) ?>
        </div>
        <div class="col-lg-6 col-md-1 col-12">
            <?= $this->render('tarjetas/tarjeta_calendario') ?>
        </div>
        <div class="col-lg-3 col-md-1 d-none d-lg-block">
            <?= Html::img('@web/images/sistema/dinomcorriendo2.png', [
                'alt' => 'Entrenamiento',
                'style' => 'max-width: 400px; height: auto; object-fit: cover; padding-top: 10px;'
            ]) ?>
        </div>


    </div>



</div>