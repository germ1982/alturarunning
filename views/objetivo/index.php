<?php

use app\helpers\AppIndexGenericoHelper;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$columnas = [
    'descripcion',
    [
        'attribute' => 'puntaje',
        'contentOptions' => ['class' => 'text-center'],
    ],
    [
        'class' => 'yii\grid\ActionColumn',
        'header' => 'Acciones',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center text-nowrap'],
        'template' => '{update} {delete}',
        'buttons' => [
            'update' => static fn ($url, $model) => Html::a('<i class="bi bi-pencil"></i>', ['update', 'id' => $model->idobjetivo], [
                'class' => 'action-btn-custom',
                'title' => 'Editar objetivo',
                'data-pjax' => 0,
            ]),
            'delete' => static fn ($url, $model) => Html::a('<i class="bi bi-trash"></i>', ['delete', 'id' => $model->idobjetivo], [
                'class' => 'action-btn-custom',
                'title' => 'Eliminar objetivo',
                'data-method' => 'post',
                'data-confirm' => '¿Eliminar este objetivo?',
            ]),
        ],
    ],
];
?>
<div class="d-flex justify-content-end mt-4">
    <?= Html::a('<i class="fas fa-reply"></i> Alumnos', ['alumno/index'], ['class' => 'btn btn-outline-light btn-sm']) ?>
</div>
<?= AppIndexGenericoHelper::renderIndex($this, 'Objetivos', 'objetivo-pjax-container', $columnas, $dataProvider, null, ['btnTitle' => 'Nuevo Objetivo']) ?>
