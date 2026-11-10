<?php

use app\helpers\AppIndexGenericoHelper;

/** @var yii\web\View $this */
/** @var app\models\EventoSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

echo AppIndexGenericoHelper::renderIndex(
    $this,
    'Administrar Eventos',
    'evento-pjax-container',
    require __DIR__ . '/_columns.php',
    $dataProvider,
    $searchModel,
    ['btnTitle' => 'Nuevo Evento']
);
