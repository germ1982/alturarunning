<?php

namespace app\components;

use Yii;
use yii\web\View;

class Mensaje
{
    public static function registerJs()
    {
        $baseUrl = Yii::$app->request->baseUrl;
        
        // Inyectar el CSS
        $css = file_get_contents(__DIR__ . '/Mensaje.css');
        Yii::$app->view->registerCss($css);

        // Inyectar el JS
        $js = "window.baseUrl = '" . $baseUrl . "';\n" . file_get_contents(__DIR__ . '/Mensaje.js');
        Yii::$app->view->registerJs($js, View::POS_HEAD);
    }

    public static function json($mensaje, $success = true, $forceReload = '#crud-datatable-pjax')
    {
        return [
            'success' => $success,
            'message' => $mensaje,
            'forceReload' => $forceReload
        ];
    }
}