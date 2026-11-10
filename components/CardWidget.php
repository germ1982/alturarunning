<?php

namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;

class CardWidget extends Widget
{
    public $title = '';
    public $content = '';
    public $footer = '';
    public $options = [];

    public function run()
    {
        // Los estilos viven en web/css/site.css (sección "Tarjetas").
        $cssClass = 'card card-generica h-100 ' . ($this->options['class'] ?? '');
        
        $html = '<div class="' . Html::encode($cssClass) . '">';
        
        if (!empty($this->title)) {
            $html .= '<div class="card-header fx-spotlight">' . $this->title . '</div>';
        }
        
        $html .= '<div class="card-body">' . $this->content . '</div>';
        
        if (!empty($this->footer)) {
            $html .= '<div class="card-footer">' . $this->footer . '</div>';
        }
        
        $html .= '</div>';

        return $html;
    }
}