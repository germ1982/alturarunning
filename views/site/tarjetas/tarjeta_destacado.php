<?php

use app\components\CardWidget;
use app\models\ConstantesGlobales;
use yii\helpers\Html;
?>  
<?= CardWidget::widget([
    'title' => 'Destacados',
    'content' => '
        <div class="px-0 pb-0" >
        <p style ="margin-top: 20%;">Villa El Chocon 28 de Julio del 2024</p>
            <div class="ratio ratio-16x9 rounded overflow-hidden" style="border: 1px solid rgba(255, 51, 51, 0.4); box-shadow: 0 0 10px rgba(255, 51, 51, 0.3);">
                <iframe 
                    src="https://www.youtube.com/embed/np3_g29e8zs?autoplay=1&mute=1&controls=0&loop=1&playlist=np3_g29e8zs" 
                    title="Altura Running en Villa El Chocón" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen>
                </iframe>
            </div>
        </div>',
    'footer' => false,
]) ?>