<?php

use app\helpers\AppIndexGenericoHelper;

/** @var yii\web\View $this */
/** @var app\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Usuarios';

// Invocamos el helper que renderiza toda la estructura Stark sin modales
echo AppIndexGenericoHelper::renderIndex(
    $this,
    $this->title,
    'user-pjax-container',
    require(__DIR__ . '/_columns.php'),
    $dataProvider,
    $searchModel,
    [
        'btnTitle' => 'Nuevo Usuario',
    ]
);