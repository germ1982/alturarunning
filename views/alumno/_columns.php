<?php

/** @var app\models\AlumnoSearch $searchModel */

use app\models\ConstantesGlobales;
use yii\helpers\Html;

return [
    [
        'attribute' => 'nombre',
        'value' => static fn ($model) => $model->persona->apellido . ', ' . $model->persona->nombre,
    ],
    [
        'attribute' => 'documento',
        'value' => 'persona.documento',
    ],
    [
        'attribute' => 'rol',
        'label' => 'Modalidad',
        'value' => static fn ($model) => $model->getRolTexto(),
        'filter' => Html::activeDropDownList($searchModel, 'rol', ConstantesGlobales::ROLES_ALUMNO, [
            'class' => 'form-select',
            'prompt' => 'Todas',
        ]),
    ],
    [
        'attribute' => 'estado',
        'value' => static fn ($model) => $model->getEstadoTexto(),
        'filter' => Html::activeDropDownList($searchModel, 'estado', ConstantesGlobales::ESTADOS_ALUMNO, [
            'class' => 'form-select',
            'prompt' => 'Todos',
        ]),
    ],
    [
        'label' => 'Sin saldar',
        'contentOptions' => ['class' => 'text-center'],
        'value' => static fn ($model) => $model->getCuotasAdeudadas(),
    ],
    [
        'class' => 'yii\grid\ActionColumn',
        'header' => 'Acciones',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center text-nowrap'],
        'template' => '{ver} {update} {delete}',
        'buttons' => [
            'ver' => static fn ($url, $model) => Html::a('<i class="bi bi-eye"></i>', ['ver', 'id' => $model->idalumno], [
                'class' => 'action-btn-custom',
                'title' => 'Ver ficha',
                'data-pjax' => 0,
            ]),
            'update' => static fn ($url, $model) => Html::a('<i class="bi bi-pencil"></i>', ['update', 'id' => $model->idalumno], [
                'class' => 'action-btn-custom',
                'title' => 'Editar alumno',
                'data-pjax' => 0,
            ]),
            'delete' => static fn ($url, $model) => Html::a('<i class="bi bi-trash"></i>', ['delete', 'id' => $model->idalumno], [
                'class' => 'action-btn-custom',
                'title' => 'Eliminar alumno',
                'data-method' => 'post',
                'data-confirm' => '¿Eliminar a ' . $model->persona->getNombreCompleto() . ' como alumno? Sus datos personales se conservan.',
            ]),
        ],
    ],
];
