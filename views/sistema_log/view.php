<?php

use app\models\SistemaLog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\SistemaLog $model */

$this->title = 'Registro de auditoría Nº ' . $model->id;
?>
<div class="neon-container mt-5 mb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div class="neon-title-container"><?= Html::encode($this->title) ?></div>
        <?= Html::a('<i class="fas fa-reply"></i> Volver', ['index'], ['class' => 'btn btn-outline-light btn-sm']) ?>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-5">
            <?= DetailView::widget([
                'model' => $model,
                'options' => ['class' => 'table table-sm table-stark'],
                'attributes' => [
                    [
                        'attribute' => 'fecha',
                        'value' => date('d/m/Y H:i:s', strtotime($model->fecha)),
                    ],
                    [
                        'attribute' => 'idusuario',
                        'value' => $model->usuario->username ?? 'Invitado',
                    ],
                    [
                        'attribute' => 'idmodulo',
                        'value' => SistemaLog::getModuloNombre($model->idmodulo),
                    ],
                    [
                        'attribute' => 'idaccion',
                        'value' => SistemaLog::getAccionNombre($model->idaccion),
                    ],
                    [
                        'attribute' => 'id_registro_afectado',
                        'value' => $model->id_registro_afectado ?? '—',
                    ],
                ],
            ]) ?>
        </div>

        <div class="col-12 col-lg-7">
            <h5 class="text-danger mb-2"><i class="bi bi-file-text me-2"></i>Descripción</h5>
            <div style="white-space: pre-wrap; overflow-wrap: anywhere; line-height: 1.6;"><?= Html::encode($model->descripcion) ?></div>
        </div>
    </div>
</div>
