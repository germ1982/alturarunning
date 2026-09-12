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
        $this->registerCss();

        $options = $this->options;
        $cssClass = 'btn-neon-pill ' . ($options['class'] ?? '');
        $options['class'] = trim($cssClass);

        $html = Html::beginTag('a', array_merge(['href' => $this->url], $options));
        $html .= '<span class="btn-neon-icon-wrapper">' . $this->icon . '</span>';
        $html .= '<span class="btn-neon-text">' . $this->label . '</span>';
        $html .= Html::endTag('a');

        return $html;
    }

    protected function registerCss()
    {
        $view = $this->getView();
        $css = file_get_contents(__DIR__ . '/NeonButtonWidget.css');
        $view->registerCss($css);
    }
}
