<?php

use app\helpers\AppIndexGenericoHelper;
use app\models\ConstantesGlobales;
use app\models\EventoDesafio;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$columnas = [
    [
        'attribute' => 'mes',
        'value' => static fn ($model) => $model->getMesTexto(),
    ],
    [
        'attribute' => 'tipo',
        'value' => static fn ($model) => $model->getTipoTexto(),
    ],
    [
        'attribute' => 'monto',
        'value' => static fn ($model) => EventoDesafio::formatearPrecio((float) $model->monto),
    ],
    [
        'label' => 'Pagos',
        'contentOptions' => ['class' => 'text-center'],
        'value' => static fn ($model) => count($model->pagos),
    ],
    [
        'class' => 'yii\grid\ActionColumn',
        'header' => 'Acciones',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center text-nowrap'],
        'template' => '{generar} {update} {delete}',
        'buttons' => [
            'generar' => static fn ($url, $model) => (int) $model->tipo === ConstantesGlobales::CUOTA_CLASE_NORMAL
                ? Html::a('<i class="bi bi-people"></i>', ['generar', 'id' => $model->idcuota], [
                    'class' => 'action-btn-custom',
                    'title' => 'Generar pagos pendientes para los alumnos activos',
                    'data-method' => 'post',
                    'data-confirm' => '¿Generar pagos pendientes de ' . $model->getMesTexto() . ' para los alumnos activos que todavía no la tengan?',
                ])
                : '',
            'update' => static fn ($url, $model) => Html::a('<i class="bi bi-pencil"></i>', ['update', 'id' => $model->idcuota], [
                'class' => 'action-btn-custom',
                'title' => 'Editar cuota',
                'data-pjax' => 0,
            ]),
            'delete' => static fn ($url, $model) => Html::a('<i class="bi bi-trash"></i>', ['delete', 'id' => $model->idcuota], [
                'class' => 'action-btn-custom',
                'title' => 'Eliminar cuota',
                'data-method' => 'post',
                'data-confirm' => '¿Eliminar la cuota de ' . $model->getMesTexto() . '?',
            ]),
        ],
    ],
];
?>
<div class="d-flex justify-content-end mt-4">
    <?= Html::a('<i class="fas fa-reply"></i> Alumnos', ['alumno/index'], ['class' => 'btn btn-outline-light btn-sm']) ?>
</div>
<?= AppIndexGenericoHelper::renderIndex($this, 'Cuotas', 'cuota-pjax-container', $columnas, $dataProvider, null, ['btnTitle' => 'Nueva Cuota']) ?>
