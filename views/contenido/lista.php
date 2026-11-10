<?php

/** @var yii\web\View $this */
/** @var int $tipo */
/** @var array $config */
/** @var app\models\DatoSistema[] $datos */
/** @var app\models\DatoSistema|null $imagenAbout */

use app\models\ConstantesGlobales;
use yii\helpers\Html;

$this->title = $config['titulo'];
?>
<div class="contenido-lista mt-5 mb-5">
    <?= $this->render('_encabezado', [
        'boton' => Html::a('<i class="bi bi-plus-lg"></i> Agregar', ['dato', 'tipo' => $tipo], ['class' => 'btn btn-outline-light btn-sm']),
    ]) ?>
    <p class="evento-tenue mb-4"><?= Html::encode($config['ayuda']) ?></p>

    <?php if ($tipo === ConstantesGlobales::ABOUT_PARRAFO): ?>
        <div class="evento-panel p-3 mb-4">
            <h2 class="evento-seccion">Imagen</h2>
            <div class="row g-3 align-items-center">
                <div class="col-12 col-md-4">
                    <?php if ($imagenAbout !== null): ?>
                        <?= Html::img('@web/images/sistema/about/' . $imagenAbout->dato, ['class' => 'w-100 rounded', 'style' => 'max-height: 180px; object-fit: cover;', 'alt' => 'Imagen de Acerca de']) ?>
                    <?php else: ?>
                        <p class="evento-tenue mb-0">Sin imagen.</p>
                    <?php endif; ?>
                </div>
                <div class="col-12 col-md-8">
                    <?= Html::beginForm(['about-imagen'], 'post', ['enctype' => 'multipart/form-data', 'class' => 'd-flex flex-wrap gap-2 align-items-end']) ?>
                        <label class="form-label mb-0 flex-grow-1">Cambiar imagen
                            <input type="file" data-comprimir="1280" name="imagen" class="form-control" accept=".jpg,.jpeg,.png,.webp" required>
                        </label>
                        <?= Html::submitButton('<i class="bi bi-upload"></i> Subir', ['class' => 'btn btn-outline-light']) ?>
                    <?= Html::endForm() ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($datos === []): ?>
        <p class="text-warning">Todavía no hay nada cargado.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-stark align-middle mb-0">
                <thead>
                    <tr>
                        <th><?= Html::encode($config['campo']) ?></th>
                        <?php if ($config['extra'] !== null): ?>
                            <th><?= Html::encode($config['extra']) ?></th>
                        <?php endif; ?>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datos as $dato): ?>
                        <tr>
                            <td style="overflow-wrap: anywhere;"><?= Html::encode($dato->dato) ?></td>
                            <?php if ($config['extra'] !== null): ?>
                                <td style="min-width: 200px; overflow-wrap: anywhere;"><?= Html::encode(mb_strimwidth((string) $dato->observacion, 0, 140, '...')) ?></td>
                            <?php endif; ?>
                            <td class="text-center text-nowrap">
                                <?= Html::a('<i class="bi bi-pencil"></i>', ['dato', 'tipo' => $tipo, 'id' => $dato->iddato], ['class' => 'action-btn-custom', 'title' => 'Editar']) ?>
                                <?= Html::a('<i class="bi bi-trash"></i>', ['dato-eliminar', 'id' => $dato->iddato], [
                                    'class' => 'action-btn-custom',
                                    'title' => 'Eliminar',
                                    'data-method' => 'post',
                                    'data-confirm' => '¿Eliminar "' . mb_strimwidth($dato->dato, 0, 60, '...') . '"?',
                                ]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
