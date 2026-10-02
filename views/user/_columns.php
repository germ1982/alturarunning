<?php

/** @var yii\web\View $this */
/** @var app\models\SistemaLogSearch $searchModel */

use yii\helpers\Html;
use yii\helpers\Url;

return [

      'username',
      [
            'attribute' => 'email',
            'value' => 'persona.email',
      ],
      [
            'attribute' => 'telefono',
            'format' => 'raw',
            'value' => function ($model) {
                  $telefono = $model->persona->telefono ?? null;
                  if (empty($telefono)) return '<span class="text-muted">No cargado</span>';

                  return Html::a('<i class="bi bi-telephone-outbound me-1"></i>' . $telefono, Url::to(['user/contacto', 'id' => $model->id]), [
                        'class' => 'text-success p-0 text-decoration-none fw-bold',
                        'title' => 'Opciones de contacto',
                  ]);
            },
      ],

      [
            'attribute' => 'status',
            'label' => 'Estado',
            'format' => 'raw',
            'value' => function ($model) {
                  if ($model->status === \app\models\User::STATUS_ACTIVE) {
                        return '<span class="badge bg-success">Activo</span>';
                  }
                  return '<span class="badge bg-danger">Inactivo</span>';
            },
            'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'status', [
                  \app\models\User::STATUS_ACTIVE => 'Activo',
                  \app\models\User::STATUS_DELETED => 'Inactivo',
            ], [
                  'class' => 'form-select',
                  'prompt' => 'Todos',
            ]),
      ],
      [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Acciones',
            'headerOptions' => ['class' => 'text-primary text-center', 'style' => 'width:150px'],
            'contentOptions' => ['class' => 'text-center'],
            'template' => '{view} {update} {roles} {toggle} {password}',
            'buttons' => [
                  'view' => function ($url, $model) {
                        return Html::a('<i class="bi bi-eye"></i>', ['view', 'id' => $model->id], [
                              'class' => 'action-btn-custom',
                              'title' => 'Detalle de Usuario ' . $model->username
                        ]);
                  },
                  'update' => function ($url, $model) {
                        return Html::a('<i class="bi bi-pencil"></i>', ['update', 'id' => $model->id], [
                              'class' => 'action-btn-custom',
                              'title' => 'Editar Usuario ' . $model->username
                        ]);
                  },
                  'toggle' => function ($url, $model) {
                        $esActivo = ($model->status == 10);
                        $icon = $esActivo ? 'bi-x-circle text-danger' : 'bi-check-circle text-success';
                        $title = $esActivo ? 'Desactivar' : 'Activar';
                        $action = $esActivo ? 'desactivar' : 'activar';

                        // Requiere POST por seguridad, usamos enlace con data-method post y confirmación
                        return Html::a('<i class="bi ' . $icon . '"></i>', [$action, 'id' => $model->id], [
                              'class' => 'action-btn-custom',
                              'title' => $title,
                              'data-method' => 'post',
                              'data-confirm' => '¿Estás seguro que querés ' . strtolower($title) . ' a ' . $model->username . '?',
                        ]);
                  },

                  'roles' => function ($url, $model, $key) {
                        return Html::a('<i class="bi bi-list"></i>', ['roles', 'id' => $key], [
                              'class' => 'action-btn-custom',
                              'title' => 'Gestionar Roles de ' . $model->username
                        ]);
                  },

                  'password' => function ($url, $model) {
                        return Html::a('<i class="bi bi-lock"></i>', ['reset-password', 'id' => $model->id], [
                              'class' => 'action-btn-custom',
                              'title' => 'Resetear contraseña',
                              'data-method' => 'post',
                              'data-confirm' => '¿Estás seguro que querés resetear la contraseña de ' . $model->username . ' a 123456?',
                        ]);
                  },

            ],
      ],
];