<?php

use yii\widgets\DetailView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = 'Usuario: ' . $model->username;
?>

<div class="neon-container mt-5">
      <div class="neon-title-container pb-3"><?= Html::encode($this->title) ?></div>

      <div class="row g-4">
            <div class="col-md-6">
                  <h5 class="mb-3 text-danger"><i class="bi bi-person-badge me-2"></i>Datos Personales</h5>
                  <?= DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table table-sm table-stark'],
                        'attributes' => [
                              'username',
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

            <div class="col-md-6">
                  <h5 class="mb-3 text-danger"><i class="bi bi-shield-lock me-2"></i>Roles Asignados</h5>

                  <?php if (!empty($model->roles)): ?>
                        <ul class="list-unstyled mb-0">
                              <?php foreach ($model->roles as $rol): ?>
                                    <li class="mb-2">
                                          <i class="bi bi-check-circle-fill text-success me-2"></i>
                                          <strong><?= Html::encode($rol->nombre) ?>:</strong>
                                          <?= Html::encode($rol->descripcion) ?>
                                    </li>
                              <?php endforeach; ?>
                        </ul>
                  <?php else: ?>
                        <p class="mb-0"><i class="bi bi-person-exclamation me-2"></i>Este usuario no tiene roles asignados.</p>
                  <?php endif; ?>
            </div>
      </div>

      <div class="text-center mt-3">
            <?= Html::a('Volver', ['index'], ['class' => 'btn btn-outline-light btn-sm']) ?>
      </div>
</div>
