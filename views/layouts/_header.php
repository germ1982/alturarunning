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

    [
        'label' => 'Administrar',
        'url' => ['/site/administrar'],
    ],
    //habilitar cuando haya contro de usuarios
     /* $g ? '':[
            'label' => mb_strtoupper(Yii::$app->user->identity->username),
            'url' => ['/site/panel_administrar', 'id' => Yii::$app->user->id]
      ] */
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
                <?php if (Yii::$app->user->isGuest): ?>
                    <a href="<?= \yii\helpers\Url::to(['/site/login']) ?>" class="nav-link header-login-btn d-flex align-items-center gap-2 px-3 py-1">
                        <span>Ingresar</span>
                    </a>
                <?php else: ?>
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-light small opacity-75">
                            <?= Html::encode(Yii::$app->user->identity?->username ?? '') ?>
                        </span>
                        <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'd-inline'])
                            . Html::submitButton('Logout', ['class' => 'nav-link logout header-logout-btn border-0 bg-transparent p-0'])
                            . Html::endForm() ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php NavBar::end(); ?>
        </div>
    </div>
</header>