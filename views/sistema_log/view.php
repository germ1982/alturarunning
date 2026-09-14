<?php
use app\models\SistemaLog;
use yii\widgets\DetailView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\SistemaLog $model */
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 border-end">
            <h5 class="mb-3 text-primary"><i class="bi bi-info-circle me-2"></i>Información General</h5>
            <?= DetailView::widget([
                'model' => $model,
                'options' => ['class' => 'table table-sm'],
                'attributes' => [
                    [
                        'attribute' => 'fecha',
                        'label' => 'Fecha',
                        'value' => function ($model) {
                            return date('d/m/Y H:i:s', strtotime($model->fecha));
                        },
                    ],
                    [
                        'attribute' => 'usuario.username',
                        'label' => 'Usuario',
                        'value' => $model->usuario ? Html::encode($model->usuario->username) : 'Sistema',
                    ],
                    [
                        'attribute' => 'idmodulo',
                        'label' => 'Módulo',
                        'value' => SistemaLog::getModuloNombre($model->idmodulo),
                    ],
                    [
                        'attribute' => 'idaccion',
                        'label' => 'Acción',
                        'value' => SistemaLog::getAccionNombre($model->idaccion),
                    ],
                    [
                        'attribute' => 'id_registro_afectado',
                        'label' => 'Registro Afectado',
                        'value' => $model->id_registro_afectado ?? 'N/A',
                    ],
                ],
            ]) ?>
        </div>

        <div class="col-md-6 ps-4">
            <h5 class="mb-3 text-primary"><i class="bi bi-file-text me-2"></i>Descripción</h5>
            <div class="alert alert-light border">
                <?= nl2br(Html::encode($model->descripcion)) ?>
            </div>
        </div>
    </div>
</div>
