<?php

use app\helpers\AppIndexGenericoHelper;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\AlumnoSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
?>
<div class="d-flex flex-wrap justify-content-end gap-2 mt-4">
    <?= Html::a('<i class="bi bi-cash-coin"></i> Cuotas', ['cuota/index'], ['class' => 'btn btn-outline-light btn-sm']) ?>
    <?= Html::a('<i class="bi bi-bullseye"></i> Objetivos', ['objetivo/index'], ['class' => 'btn btn-outline-light btn-sm']) ?>
    <?= Html::a('<i class="bi bi-folder2-open"></i> Tipos de documentación', ['documentacion-tipo/index'], ['class' => 'btn btn-outline-light btn-sm']) ?>
</div>
<?= AppIndexGenericoHelper::renderIndex(
    $this,
    'Administrar Alumnos',
    'alumno-pjax-container',
    require __DIR__ . '/_columns.php',
    $dataProvider,
    $searchModel,
    ['btnTitle' => 'Nuevo Alumno']
) ?>
