<?php

/** @var yii\web\View $this */

use app\components\CardWidget;
use app\models\ConstantesGlobales;
use yii\helpers\Html;

$this->title = 'Acerca de Altura Running';

$parrafos = (new \yii\db\Query())
    ->select(['ds.dato', 'ds.observacion'])
    ->from('dato_sistema ds')
    ->where(['ds.idtipo' => ConstantesGlobales::ABOUT_PARRAFO])
    ->all();

$htmlContent = '';

foreach ($parrafos as $parrafo) {
    $htmlContent .= '<h5 class="text-acento fw-bold mb-2">' . Html::encode($parrafo['dato']) . '</h5>
                    <p class="mb-4">' . nl2br(Html::encode($parrafo['observacion'])) . '</p>';
}

// La imagen se cambia en Administrar > Contenidos > Acerca de.
$imagen = (new \yii\db\Query())
    ->select('dato')
    ->from('dato_sistema')
    ->where(['idtipo' => ConstantesGlobales::ABOUT_IMAGEN])
    ->scalar();
?>
<div class="site-about mt-5">
    <div class="row g-3 mb-5 justify-content-center">
        <div class="col-12 col-lg-8">
            <?= CardWidget::widget([
                'title' => 'Acerca de Altura Running',
                'content' => '
                    <div class="text-center py-3">
                        ' . ($imagen ? '<div class="mb-4">' . Html::img('@web/images/sistema/about/' . $imagen, [
                                'alt' => 'Altura Running',
                                'class' => 'img-fluid rounded',
                                'style' => 'max-height: 250px; object-fit: cover;'
                            ]) . '</div>' : '') . '
                        <div class="text-start text-white px-3" style="font-size: 0.9rem; line-height: 1.6;">
                            ' . $htmlContent . '
                        </div>
                    </div>',
                'footer' => false,
            ]) ?>
        </div>
    </div>
</div>