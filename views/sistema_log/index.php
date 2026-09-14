<?php

use app\helpers\AppIndexGenericoHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TuModeloSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

/** @var app\models\Sistema_log $dataProvider */
/** @var app\models\Sistema_log $searchModel */

// Cargamos las columnas desde el archivo separado
$columns = require(__DIR__ . '/_columns.php');

// Renderizamos todo el motor con una sola línea
echo AppIndexGenericoHelper::renderIndex(
      $this,
      'Auditorias del Sistema',
      'datos-pjax-container', // El ID para el PJAX
      $columns,
      $dataProvider,
      $searchModel,
      [
            'btnAlta' => false,
            'btnTitle' => 'Nuevo',
            'modalSize' => \yii\bootstrap5\Modal::SIZE_LARGE
      ],
);
