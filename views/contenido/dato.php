<?php

/** @var yii\web\View $this */
/** @var int $tipo */
/** @var array $config */
/** @var app\models\DatoSistema $dato */

use yii\helpers\Html;

$this->title = ($dato->isNewRecord ? 'Agregar a ' : 'Editar en ') . $config['titulo'];
?>
<div class="contenido-dato row justify-content-center mt-5 mb-5">
    <div class="col-12 col-md-8 col-lg-6">
        <?= $this->render('_encabezado', ['volver' => ['lista', 'tipo' => $tipo]]) ?>

        <div class="neon-container">
            <?= Html::beginForm('', 'post') ?>
                <?php foreach ($dato->getFirstErrors() as $error): ?>
                    <div class="text-danger small mb-2"><?= Html::encode($error) ?></div>
                <?php endforeach; ?>

                <div class="mb-3">
                    <label class="form-label w-100"><?= Html::encode($config['campo']) ?>
                        <?php if ($config['extra'] === null && mb_strlen((string) $dato->dato) > 80): ?>
                            <?= Html::textarea('dato', $dato->dato, ['class' => 'form-control', 'rows' => 3, 'required' => true]) ?>
                        <?php else: ?>
                            <?= Html::textInput('dato', $dato->dato, ['class' => 'form-control', 'required' => true, 'maxlength' => 2000]) ?>
                        <?php endif; ?>
                    </label>
                </div>

                <?php if ($config['extra'] !== null): ?>
                    <div class="mb-3">
                        <label class="form-label w-100"><?= Html::encode($config['extra']) ?>
                            <?= Html::textarea('observacion', $dato->observacion, ['class' => 'form-control', 'rows' => 8]) ?>
                        </label>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-center gap-3 mt-4">
                    <?= Html::submitButton('Guardar', ['class' => 'btn btn-outline-light']) ?>
                    <?= Html::a('Cancelar', ['lista', 'tipo' => $tipo], ['class' => 'btn btn-outline-light']) ?>
                </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>
