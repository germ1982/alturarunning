<?php

/** @var yii\web\View $this */

use app\components\CardWidget;
use yii\helpers\Html;

$this->title = 'En Construcción';
?>
<div class="site-en-construccion">
    <div class="row g-3 mb-5 justify-content-center mt-5">
        <div class="col-12 col-lg-6 text-center">
            <?= CardWidget::widget([
                'title' => 'Próximamente',
                'content' => '
                    <div class="text-center py-4">
                        <p class="text-warning fw-bold fs-5 mb-4">EN CONSTRUCCIÓN</p>
                        <div class="mb-3">
                            ' . Html::img('@web/images/sistema/dinomfestejando.png', [
                                'alt' => 'En Construcción',
                                'class' => 'img-fluid',
                                'style' => 'max-height: 280px; object-fit: contain;'
                            ]) . '
                        </div>
                        <p class="text-white-50 mt-3" style="font-size: 0.85rem;">Estamos trabajando para ofrecerte esta sección muy pronto.</p>
                    </div>',
                'footer' => '<div class="text-center">' . Html::a('Volver al Inicio', ['site/index'], ['class' => 'btn btn-outline-light btn-sm']) . '</div>',
            ]) ?>
        </div>
    </div>
</div>