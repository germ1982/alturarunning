<?php

/** @var yii\web\View $this */

use app\components\CardWidget;
use app\components\NeonButtonWidget;
use yii\helpers\Html;

$this->title = 'Panel de Control';
?>
<div class="site-panel-control">
    <div class="row g-3 mb-5 justify-content-center mt-5">
        <div class="col-12 col-lg-6 text-center">
            <div class="neon-container d-inline-block p-4" style="min-width: 320px;">
                <h2 class="neon-title-container pb-4 text-center w-100">Panel de Control</h2>

                <div class="d-flex flex-column align-items-start" style="padding-left: 25px;">

                    <div class="my-2">
                        <?= NeonButtonWidget::widget([
                            'label' => 'Usuarios',
                            'url' => \yii\helpers\Url::to(['site/gestionar_usuarios']),
                            'icon' => '<i class="fas fa-user-gear fa-beat"></i>',
                        ]); ?>
                    </div>
                    <div class="my-2">
                        <?= NeonButtonWidget::widget([
                            'label' => 'Contenidos',
                            'url' => \yii\helpers\Url::to(['site/gestionar_contenidos']),
                            'icon' => '<i class="fas fa-gear fa-spin"></i>',
                        ]); ?>
                    </div>

                    <div class="my-2">
                        <?= NeonButtonWidget::widget([
                            'label' => 'Eventos',
                            'url' => \yii\helpers\Url::to(['site/gestionar_eventos']),
                            'icon' => '<i class="fas fa-calendar-days fa-fade"></i>',
                        ]); ?>
                    </div>
                    <div class="my-2">
                        <?= NeonButtonWidget::widget([
                            'label' => 'Alumnos',
                            'url' => \yii\helpers\Url::to(['site/gestionar_alumnos']),
                            'icon' => '<i class="fas fa-running fa-bounce"></i>',
                        ]); ?>
                    </div>

                    <div class="my-2">
                        <?= NeonButtonWidget::widget([
                            'label' => 'Profesores',
                            'url' => \yii\helpers\Url::to(['site/gestionar_profesores']),
                            'icon' => '<i class="fas fa-chalkboard-teacher fa-shake"></i>',
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>