<?php

use app\helpers\AppIndexGenericoHelper;

/** @var yii\web\View $this */
/** @var app\models\SistemaLogSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

echo AppIndexGenericoHelper::renderIndex(
    $this,
    'Auditorías',
    'auditoria-pjax-container',
    require __DIR__ . '/_columns.php',
    $dataProvider,
    $searchModel,
    ['btnAlta' => false]
);
