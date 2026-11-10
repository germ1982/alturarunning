<?php

use app\helpers\AppIndexGenericoHelper;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$columnas = [
    'descripcion',
    [
        'class' => 'yii\grid\ActionColumn',
        'header' => 'Acciones',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center text-nowrap'],
        'template' => '{update} {delete}',
        'buttons' => [
            'update' => static fn ($url, $model) => Html::a('<i class="bi bi-pencil"></i>', ['update', 'id' => $model->idtipo_documentacion], [
                'class' => 'action-btn-custom',
                'title' => 'Editar tipo',
                'data-pjax' => 0,
            ]),
            'delete' => static fn ($url, $model) => Html::a('<i class="bi bi-trash"></i>', ['delete', 'id' => $model->idtipo_documentacion], [
                'class' => 'action-btn-custom',
                'title' => 'Eliminar tipo',
                'data-method' => 'post',
                'data-confirm' => '¿Eliminar el tipo ' . $model->descripcion . '?',
            ]),
        ],
    ],
];
?>
<div class="d-flex justify-content-end mt-4">
    <?= Html::a('<i class="fas fa-reply"></i> Alumnos', ['alumno/index'], ['class' => 'btn btn-outline-light btn-sm']) ?>
</div>
<?= AppIndexGenericoHelper::renderIndex($this, 'Tipos de documentación', 'documentacion-tipo-pjax-container', $columnas, $dataProvider, null, ['btnTitle' => 'Nuevo Tipo']) ?>
