<?php

/** @var yii\web\View $this */
/** @var app\models\SistemaLogSearch $searchModel */

use app\models\SistemaLog;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$usuarios = ArrayHelper::map(User::find()->select(['id', 'username'])->orderBy(['username' => SORT_ASC])->all(), 'id', 'username');

return [
    [
        'attribute' => 'fecha',
        'contentOptions' => ['class' => 'text-nowrap'],
        'value' => static fn ($model) => date('d/m/Y H:i', strtotime($model->fecha)),
        'filter' => Html::tag(
            'div',
            Html::activeInput('date', $searchModel, 'fecha_desde', ['class' => 'form-control form-control-sm mb-2', 'style' => 'color-scheme: dark;', 'title' => 'Desde']) .
            Html::activeInput('date', $searchModel, 'fecha_hasta', ['class' => 'form-control form-control-sm', 'style' => 'color-scheme: dark;', 'title' => 'Hasta']),
            ['style' => 'min-width: 150px;']
        ),
    ],
    [
        'attribute' => 'idusuario',
        'value' => static fn ($model) => $model->usuario->username ?? 'Invitado',
        'filter' => Html::activeDropDownList($searchModel, 'idusuario', $usuarios, ['class' => 'form-select', 'prompt' => 'Todos']),
    ],
    [
        'attribute' => 'idmodulo',
        'value' => static fn ($model) => SistemaLog::getModuloNombre($model->idmodulo),
        'filter' => Html::activeDropDownList($searchModel, 'idmodulo', SistemaLog::getListaModulos(), ['class' => 'form-select', 'prompt' => 'Todos']),
    ],
    [
        'attribute' => 'idaccion',
        'value' => static fn ($model) => SistemaLog::getAccionNombre($model->idaccion),
        'filter' => Html::activeDropDownList($searchModel, 'idaccion', SistemaLog::getListaAcciones(), ['class' => 'form-select', 'prompt' => 'Todas']),
    ],
    [
        'attribute' => 'descripcion',
        'contentOptions' => ['style' => 'min-width: 220px; overflow-wrap: anywhere;'],
        'value' => static function ($model) {
            $primeraLinea = strtok($model->descripcion, "\n");

            return mb_strlen($primeraLinea) > 90 ? mb_substr($primeraLinea, 0, 87) . '...' : $primeraLinea;
        },
        'filter' => Html::activeInput('text', $searchModel, 'buscar', ['class' => 'form-control form-control-sm', 'placeholder' => 'Buscar...']),
    ],
    [
        'class' => 'yii\grid\ActionColumn',
        'header' => 'Detalle',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
        'template' => '{view}',
        'buttons' => [
            'view' => static fn ($url, $model) => Html::a('<i class="bi bi-eye"></i>', ['view', 'id' => $model->id], [
                'class' => 'action-btn-custom',
                'title' => 'Ver registro Nº ' . $model->id,
                'data-pjax' => 0,
            ]),
        ],
    ],
];
