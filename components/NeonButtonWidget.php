<?php

namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;

class NeonButtonWidget extends Widget
{
    public $label = '';
    public $url = '#';
    public $icon = '<i class="fas fa-arrow-right"></i>'; // Icono por defecto (FontAwesome)
    public $options = [];

public function run()
    {
        // Los estilos viven en web/css/site.css (sección "Botones y etiquetas").
        $options = $this->options;
        $cssClass = 'btn-neon-pill ' . ($options['class'] ?? '');
        $options['class'] = trim($cssClass);

        $html = Html::beginTag('a', array_merge(['href' => $this->url], $options));
        $html .= '<span class="btn-neon-icon-wrapper">' . $this->icon . '</span>';
        $html .= '<span class="btn-neon-text">' . $this->label . '</span>';
        $html .= Html::endTag('a');

        return $html;
    }
}
