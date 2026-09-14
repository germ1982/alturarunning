<?php

/** @var yii\web\View $this */
/** @var app\models\SistemaLogSearch $searchModel */

use yii\helpers\Html;

return [

      'username',
      'email:email',
      [
            'attribute' => 'telefono',
            'format' => 'raw',
            'value' => function ($model) {
                  if (empty($model->telefono)) return '<span class="text-muted">No cargado</span>';

                  return Html::button('<i class="bi bi-telephone-outbound me-1"></i>' . $model->telefono, [
                        'value' => \yii\helpers\Url::to(['user/contacto-modal', 'id' => $model->id]),
                        'class' => 'showModalButton btn btn-link text-success p-0 text-decoration-none fw-bold',
                        'title' => 'Opciones de contacto',
                        'data-title' => 'Contactar a ' . $model->username,
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
                  'class' => 'form-select', // <--- ESTA ES LA CLAVE PARA LA FLECHITA
                  'prompt' => 'Todos',
            ]),
      ],
      [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Acciones',
            'headerOptions' => ['class' => 'text-primary text-center', 'style' => 'width:120px'],
            'contentOptions' => ['class' => 'text-center'],
            'template' => '{view} {update} {roles} {toggle} {password}',
            'buttons' => [
                  'view' => function ($url, $model) {
                        return Html::button('<i class="bi bi-eye"></i>', [
                              'value' => $url,
                              'class' => 'action-btn-custom showModalButton',
                              'data-title' => 'Detalle de Usuario ' . $model->username, // <-- El JS leerá esto
                              'title' => 'Detalle de Usuario ' . $model->username
                        ]);
                  },
                  'update' => function ($url, $model) {
                        return Html::button('<i class="bi bi-pencil"></i>', [
                              'value' => $url,
                              'class' => 'action-btn-custom showModalButton',
                              'data-title' => 'Editar Usuario ' . $model->username, // <-- El JS leerá esto
                              'title' => 'Editar Usuario ' . $model->username
                        ]);
                  },
                  'toggle' => function ($url, $model) {
                        $esActivo = ($model->status == 10);
                        $icon = $esActivo ? 'bi-x-circle text-danger' : 'bi-check-circle text-success';
                        $title = $esActivo ? 'Desactivar' : 'Activar';
                        $action = $esActivo ? 'desactivar' : 'activar';

                        return Html::button('<i class="bi ' . $icon . '"></i>', [
                              'value' => \yii\helpers\Url::to(['user/' . $action, 'id' => $model->id]),
                              'class' => 'action-btn-custom showModalButton', // Usamos tu clase de modal
                              'title' => $title,
                              'data-title' => $title . ' Usuario: ' . $model->username,

                        ]);
                  },


                  /* 'delete' => function ($url, $model) {
                        return Html::a('<i class="bi bi-trash"></i>', $url, [
                              'class' => 'action-btn-custom showModalButton',
                              'data-confirm' => '¿Eliminar registro?',
                              'data-method' => 'post',
                              'data-title' => 'Eliminar Usuario ' . $model->username // <-- El JS leerá esto
                        ]);
                  }, */

                  'roles' => function ($url, $model, $key) {
                        return Html::button('<i class="bi bi-list"></i>', [
                              'value' => \yii\helpers\Url::to(['user/roles', 'id' => $key]),
                              'class' => 'action-btn-custom showModalButton',
                              'data-title' => 'Gestionar Roles de ' . $model->username, // <-- El JS leerá esto
                              'title' => 'Gestionar Roles de ' . $model->username
                        ]);
                  },

                  'password' => function ($url, $model) {
                        return Html::button('<i class="bi bi-lock"></i>', [
                              'value' => \yii\helpers\Url::to(['user/reset-password', 'id' => $model->id]),
                              'class' => 'action-btn-custom showModalButton',
                              'title' => 'Resetear contraseña',
                              'data-title' => 'Resetear contraseña de ' . $model->username,
                        ]);
                  },

            ],
      ],
];
