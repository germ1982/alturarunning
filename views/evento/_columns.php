<?php

/** @var app\models\EventoSearch $searchModel */

use app\models\ConstantesGlobales;
use yii\helpers\Html;

return [
    'titulo',
    [
        'attribute' => 'fecha_evento',
        'value' => static fn ($model) => $model->getFechaCorta(),
    ],
    [
        'attribute' => 'estado',
        'format' => 'raw',
        'value' => static fn ($model) => $model->estado == ConstantesGlobales::EVENTO_ACTIVO
            ? '<span class="badge bg-success">Activo</span>'
            : '<span class="badge bg-danger">Inactivo</span>',
        'filter' => Html::activeDropDownList($searchModel, 'estado', ConstantesGlobales::ESTADOS_EVENTO, [
            'class' => 'form-select',
            'prompt' => 'Todos',
        ]),
    ],
    [
        'label' => 'Desafíos',
        'contentOptions' => ['class' => 'text-center'],
        'value' => static fn ($model) => count($model->desafios),
    ],
    [
        'label' => 'Inscriptos',
        'contentOptions' => ['class' => 'text-center'],
        'value' => static function ($model) {
            $total = 0;

            foreach ($model->desafios as $desafio) {
                $total += count($desafio->inscripciones);
            }

            return $total;
        },
    ],
    [
        'label' => 'Solicitudes',
        'format' => 'raw',
        'contentOptions' => ['class' => 'text-center'],
        'value' => static fn ($model) => $model->getSolicitudesPendientes() > 0
            ? '<span class="badge bg-warning text-dark">' . $model->getSolicitudesPendientes() . '</span>'
            : '0',
    ],
    [
        'class' => 'yii\grid\ActionColumn',
        'header' => 'Acciones',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center text-nowrap'],
        'template' => '{ver} {update} {delete}',
        'buttons' => [
            'ver' => static fn ($url, $model) => Html::a('<i class="bi bi-eye"></i>', ['ver', 'id' => $model->idevento], [
                'class' => 'action-btn-custom',
                'title' => 'Ver evento',
                'data-pjax' => 0,
            ]),
            'update' => static fn ($url, $model) => Html::a('<i class="bi bi-pencil"></i>', ['update', 'id' => $model->idevento], [
                'class' => 'action-btn-custom',
                'title' => 'Editar evento',
                'data-pjax' => 0,
            ]),
            'delete' => static fn ($url, $model) => Html::a('<i class="bi bi-trash"></i>', ['delete', 'id' => $model->idevento], [
                'class' => 'action-btn-custom',
                'title' => 'Eliminar evento',
                'data-method' => 'post',
                'data-confirm' => '¿Eliminar el evento ' . $model->titulo . '? También se borran sus fotos y desafíos.',
            ]),
        ],
    ],
];
