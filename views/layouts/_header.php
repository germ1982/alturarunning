<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Html;

$g = Yii::$app->user->isGuest;

$items = [
    [
        'label' => 'Home',
        'url' => ['/site/index'],
    ],
    [
        'label' => 'Acerca Nuestro',
        'url' => ['/site/about'],
    ],
    [
        'label' => 'Eventos',
        'url' => ['/site/eventos'],
    ],
    [
        'label' => 'Alumnos',
        'url' => ['/site/alumnos'],
    ],


    $g ? '' : [
        'label' => 'Administrar',
        'url' => ['/site/panel_administrar', 'id' => Yii::$app->user->id]
    ],



];

?>
<header id="header">
    <div class="container header-wrapper d-flex align-items-center">
        <!-- Logo a la izquierda (se queda quieto ahí) -->
        <div class="header-brand-container me-3">
            <a href="<?= Yii::$app->homeUrl ?>">
                <img src="<?= \yii\helpers\Url::to('@web/images/sistema/logo.png') ?>" alt="Logo" class="header-logo">
            </a>
        </div>

        <!-- Barra translúcida -->
        <div class="header-glass-bar flex-grow-1">
            <?php NavBar::begin([
                'brandLabel' => false,
                'innerContainerOptions' => ['class' => 'container-fluid px-3 d-flex align-items-center justify-content-between'],
                'options' => ['class'  => 'navbar navbar-expand-md navbar-dark p-0 w-100'],
                'togglerContent' => '<span class="navbar-toggler-icon"></span> <span class="ms-2" style="color: #ff3333; text-shadow: 0 0 5px rgba(255,0,0,0.5);">Menú</span>',
            ]); ?>

            <?= Nav::widget([
                'options' => ['class' => 'navbar-nav me-auto align-items-start'],
                'encodeLabels' => false,
                'items' => $items,
            ]); ?>

            <!-- Botones de la derecha -->
            <div class="header-icons d-flex align-items-center gap-3">
                <?php if (\Yii::$app->user->isGuest): ?>
                    <a href="<?= \yii\helpers\Url::to(['/site/login']) ?>">INGRESAR</a>
                <?php else: ?>
                    <a href="<?= \yii\helpers\Url::to(['/site/logout']) ?>" data-method="post">SALIR</a>
                <?php endif; ?>
            </div>

            <?php NavBar::end(); ?>
        </div>
    </div>
</header>