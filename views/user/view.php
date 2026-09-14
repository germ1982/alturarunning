<?php

use yii\widgets\DetailView;
use yii\helpers\Html;


/** @var app\models\User $model */
?>

<div class="container-fluid">
      <div class="row">
            <div class="col-md-6 border-end">
                  <h5 class="mb-3 text-primary"><i class="bi bi-person-badge me-2"></i>Datos Personales</h5>
                  <?= DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table table-sm '], // Le sacamos los bordes toscos
                        'attributes' => [
                              [
                                    'attribute' => 'username',
                                    'captionOptions' => ['class' => 'fw-bold text-muted', 'style' => 'width: 30%'],
                              ],
                              'email:email',
                              'telefono',
                              [
                                    'attribute' => 'status',
                                    'format' => 'raw',
                                    'value' => $model->status === \app\models\User::STATUS_ACTIVE
                                          ? '<span class="badge bg-success">Activo</span>'
                                          : '<span class="badge bg-danger">Inactivo</span>',
                              ],
                              [
                                    'attribute' => 'created_at',
                                    'label' => 'Fecha Alta',
                                    'value' => function ($model) {
                                          return $model->created_at
                                                ? date('d/m/Y H:i', strtotime($model->created_at))
                                                : 'No definida';
                                    },
                              ]
                        ],
                  ]) ?>
            </div>

            <div class="col-md-6 ps-4">
                  <h5 class="mb-3 text-primary"><i class="bi bi-shield-lock me-2"></i>Roles Asignados</h5>

                  <?php if (!empty($model->roles)): ?>
                        <div class="d-flex flex-wrap gap-2">
                              <?php foreach ($model->roles as $rol): ?>
                                    <span class="badge border text-dark p-2 px-3 bg-light d-flex align-items-center fw-normal">
                                          <i class="bi bi-check-circle-fill text-success me-2"></i>
                                          <strong class="me-1"><?= Html::encode($rol->nombre) ?>:</strong>
                                          <span class="text-muted fw-light"><?= Html::encode($rol->descripcion) ?></span>
                                    </span>
                              <?php endforeach; ?>
                        </div>
                  <?php else: ?>
                        <div class="alert alert-light border text-muted">
                              <i class="bi bi-person-exclamation me-2"></i>Este usuario no tiene roles asignados.
                        </div>
                  <?php endif; ?>
            </div>
      </div>
</div>