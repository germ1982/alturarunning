<?php

use app\components\CardWidget;
use app\models\ConstantesGlobales;
use yii\helpers\Html;
use yii\helpers\Json;

$frasesDino = (new \yii\db\Query())
    ->select(['ds.dato'])
    ->from('dato_sistema ds')
    ->where(['ds.idtipo' => ConstantesGlobales::TIPO_FRASES_DINO])
    ->column();

if (empty($frasesDino)) {
    $frasesDino = ['¡A darlo todo en los entrenamientos y a disfrutar del camino!'];
}

$frasesJson = Json::encode($frasesDino);
?>           
        <?= CardWidget::widget([
            'title' => 'Dino Motivador',
            'content' => '
                <div class="text-center py-2" style="position: relative; display: inline-block; width: 100%; overflow: hidden;">
                    ' . Html::img('@web/images/sistema/dinomanunciando2.png', [
                        'alt' => 'Dino Motivador',
                        'style' => 'max-width: 100%; height: auto; display: block; margin: 0 auto;'
                    ]) . '
                    <div style="position: absolute; top: 50%; left: 12%; right: 12%; transform: translateY(-50%); color: #ffffff; font-size: 13px; text-align: center; padding: 0 10px; pointer-events: none;">
                        <p id="texto-dino-motivador" class="mb-0" style="transition: all 0.4s ease-in-out; opacity: 1; transform: translateY(0);">' . $frasesDino[0] . '</p>
                    </div>
                </div>',
            'footer' => false,
        ]) ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const frases = <?= $frasesJson ?>;
    if (frases.length <= 1) return;
    
    let index = 0;
    const el = document.getElementById("texto-dino-motivador");
    
    setInterval(() => {
        // 1. Sube y se desvanece
        el.style.opacity = 0;
        el.style.transform = "translateY(-15px)";
        
        setTimeout(() => {
            // 2. Cambiamos el texto y lo posicionamos abajo sin transición visible
            index = (index + 1) % frases.length;
            el.textContent = frases[index];
            el.style.transform = "translateY(15px)";
            
            // 3. Forzamos un reflow para que tome la posición inicial y baje a su lugar con opacidad
            void el.offsetWidth;
            
            el.style.opacity = 1;
            el.style.transform = "translateY(0)";
        }, 400); // Coincide con la duración de la transición CSS
        
    }, 10000);
});
</script>