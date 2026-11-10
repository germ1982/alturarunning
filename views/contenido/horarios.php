<?php

/** @var yii\web\View $this */
/** @var app\models\Calendario[] $horarios */

use app\models\ConstantesGlobales;
use yii\helpers\Html;

$this->title = 'Horarios';
?>
<div class="contenido-horarios mt-5 mb-5">
    <?= $this->render('_encabezado', [
        'boton' => Html::a('<i class="bi bi-plus-lg"></i> Nuevo horario', ['horario'], ['class' => 'btn btn-outline-light btn-sm']),
    ]) ?>
    <p class="evento-tenue mb-4">Lo que se muestra en la tarjeta de horarios del home. Los lugares y modalidades se cargan en sus propias listas.</p>

    <?php if ($horarios === []): ?>
        <p class="text-warning">Todavía no hay horarios.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-stark align-middle mb-0">
                <thead>
                    <tr><th>Lugar</th><th>Día</th><th>Horario</th><th>Modalidad</th><th>Profesor</th><th class="text-center">Acciones</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($horarios as $horario): ?>
                        <tr>
                            <td><?= Html::encode($horario->lugar->dato ?? '—') ?></td>
                            <td><?= Html::encode(ConstantesGlobales::DIAS[$horario->dia] ?? '') ?></td>
                            <td class="text-nowrap"><?= Html::encode($horario->horario) ?></td>
                            <td><?= Html::encode($horario->modalidad->dato ?? '—') ?></td>
                            <td><?= Html::encode($horario->instructor !== null ? $horario->instructor->persona->getNombreCompleto() : '—') ?></td>
                            <td class="text-center text-nowrap">
                                <?= Html::a('<i class="bi bi-pencil"></i>', ['horario', 'id' => $horario->id], ['class' => 'action-btn-custom', 'title' => 'Editar']) ?>
                                <?= Html::a('<i class="bi bi-trash"></i>', ['horario-eliminar', 'id' => $horario->id], [
                                    'class' => 'action-btn-custom',
                                    'title' => 'Eliminar',
                                    'data-method' => 'post',
                                    'data-confirm' => '¿Eliminar este horario?',
                                ]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
