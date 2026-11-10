<?php

/** @var yii\web\View $this */

use app\components\NeonButtonWidget;
use app\helpers\UserHelper;
use yii\helpers\Url;

$this->title = 'Panel de Control';

$secciones = [
    ['permiso' => 'usuarios', 'label' => 'Usuarios', 'ruta' => 'site/gestionar_usuarios', 'icon' => 'fa-user-gear fa-beat'],
    ['permiso' => 'contenidos', 'label' => 'Contenidos', 'ruta' => 'site/gestionar_contenidos', 'icon' => 'fa-gear fa-spin'],
    ['permiso' => 'eventos', 'label' => 'Eventos', 'ruta' => 'site/gestionar_eventos', 'icon' => 'fa-calendar-days fa-fade'],
    ['permiso' => 'alumnos', 'label' => 'Alumnos', 'ruta' => 'site/gestionar_alumnos', 'icon' => 'fa-running fa-bounce'],
    ['permiso' => 'profesores', 'label' => 'Profesores', 'ruta' => 'site/gestionar_profesores', 'icon' => 'fa-chalkboard-teacher fa-shake'],
];
?>
<div class="site-panel-control">
    <div class="row g-3 mb-5 justify-content-center mt-5">
        <div class="col-12 col-lg-6 text-center">
            <div class="neon-container d-inline-block p-4 w-100" style="max-width: 360px;">
                <h2 class="neon-title-container pb-4 text-center w-100">Panel de Control</h2>

                <div class="d-flex flex-column align-items-start" style="padding-left: 25px;">
                    <?php foreach ($secciones as $seccion): ?>
                        <?php if (UserHelper::puede($seccion['permiso'])): ?>
                            <div class="my-2">
                                <?= NeonButtonWidget::widget([
                                    'label' => $seccion['label'],
                                    'url' => Url::to([$seccion['ruta']]),
                                    'icon' => '<i class="fas ' . $seccion['icon'] . '"></i>',
                                ]); ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <div class="my-2">
                        <?= NeonButtonWidget::widget([
                            'label' => 'Mis Datos',
                            'url' => Url::to(['user/update', 'id' => Yii::$app->user->id]),
                            'icon' => '<i class="fas fa-id-card"></i>',
                        ]); ?>
                    </div>

                    <?php if (UserHelper::puede('aspecto')): ?>
                        <div class="my-2">
                            <?= NeonButtonWidget::widget([
                                'label' => 'Aspecto',
                                'url' => Url::to(['site/aspecto']),
                                'icon' => '<i class="fas fa-palette"></i>',
                            ]); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (UserHelper::puede('auditorias')): ?>
                        <div class="my-2">
                            <?= NeonButtonWidget::widget([
                                'label' => 'Auditorías',
                                'url' => Url::to(['sistema_log/index']),
                                'icon' => '<i class="fas fa-clipboard-list"></i>',
                            ]); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
