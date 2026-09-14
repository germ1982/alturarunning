<?php
/** @var yii\web\View $this */
/** @var app\models\SistemaLogSearch $searchModel */

use app\models\SistemaLog;
use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

$usuarios = \yii\helpers\ArrayHelper::map(
    User::find()->select(['id', 'username'])->all(),
    'id',
    'username'
);

return [
    [
        'attribute' => 'fecha',
        'label' => 'Fecha',
        'format' => 'html',
        'value' => function($model) {
            return date('d/m/Y H:i:s', strtotime($model->fecha));
        },
        'filter' => Html::tag('div',
            Html::activeInput('date', $searchModel, 'fecha_desde', ['class' => 'form-control form-control-sm mb-2', 'placeholder' => 'Desde']) .
            Html::activeInput('date', $searchModel, 'fecha_hasta', ['class' => 'form-control form-control-sm', 'placeholder' => 'Hasta']),
            ['style' => 'min-width: 150px;']
        )
    ],
    [
        'attribute' => 'usuario.username',
        'label' => 'Usuario',
        'value' => function($model) {
            return $model->usuario ? Html::encode($model->usuario->username) : 'Sistema';
        },
        'filter' => Html::activeDropDownList(
            $searchModel,
            'idusuario',
            ['' => 'Todos'] + $usuarios,
            ['class' => 'form-select']
        )
    ],
    [
        'attribute' => 'idmodulo',
        'label' => 'Módulo',
        'value' => function($model) {
            return SistemaLog::getModuloNombre($model->idmodulo);
        },
        'filter' => Html::activeDropDownList(
            $searchModel,
            'idmodulo',
            SistemaLog::getListaModulos(), // <-- Usamos la función del modelo
            ['class' => 'form-select', 'prompt' => 'Todos']
        )
    ],
    [
        'attribute' => 'idaccion',
        'label' => 'Acción',
        'value' => function($model) {
            return SistemaLog::getAccionNombre($model->idaccion);
        },
        'filter' => Html::activeDropDownList(
            $searchModel,
            'idaccion',
            SistemaLog::getListaAcciones(), // <-- Usamos la función del modelo
            ['class' => 'form-select', 'prompt' => 'Todos']
        )
    ],
    [
        'attribute' => 'descripcion',
        'label' => 'Descripción',
        'value' => function($model) {
            return Html::encode(mb_substr($model->descripcion, 0, 50)) . (mb_strlen($model->descripcion) > 50 ? '...' : '');
        },
        'filter' => Html::activeInput('text', $searchModel, 'buscar', ['class' => 'form-control form-control-sm', 'placeholder' => 'Buscar...'])
    ],
    [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{view}',
        'buttons' => [
            'view' => function ($url, $model) {
                return Html::button('<i class="bi bi-eye"></i>', [
                    'value' => $url,
                    'class' => 'btn btn-sm btn-outline-primary showModalButton',
                    'data-title' => 'Ver Log #' . $model->id,
                ]);
            }
        ]
    ]
];