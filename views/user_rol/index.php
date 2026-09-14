<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\Mensaje;

$this->title = 'Gestión de Roles';
$this->registerJs(file_get_contents(__DIR__ . '/index.js'), \yii\web\View::POS_END);
/** @var yii\web\View $roles */
?>




<div class="row g-3 mb-5 justify-content-center mt-3">
    <div class="col-12 col-lg-8">

        <h2 class="neon-title-container mb-4"><?= Html::encode($this->title) ?></h2>

        <?php $form = ActiveForm::begin([
            'action' => ['crear'],
            'method' => 'get',
            'id' => 'form-crear-rol',
        ]); ?>
        <div class="input-group mb-4" style="max-width: 400px;">
            <?= Html::textInput('nombre', '', ['placeholder' => 'Nombre del rol (ej: motu)', 'class' => 'form-control', 'autocomplete' => 'off', 'id' => 'input-nombre-rol']) ?>
            <div class="input-group-append">
                <?= Html::button('Crear Rol', ['class' => 'btn btn-danger', 'id' => 'btn-crear-rol']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>

        <h2 class="neon-title-container mb-3">Roles Existentes:</h2>

<table class="table table-dark table-stark mt-3">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roles as $role): ?>
                    <tr>
                        <td><?= Html::encode($role->name) ?></td>
                        <td><?= Html::encode($role->description) ?></td>
                        <td class="text-center">
                            <?= Html::button('<i class="fa fa-edit"></i>', [
                                'class' => 'btn btn-sm btn-outline-warning btn-editar-rol',
                                'data-nombre' => $role->name,
                                'title' => 'Editar'
                            ]) ?>
                            <?= Html::button('<i class="fa fa-trash"></i>', [
                                'class' => 'btn btn-sm btn-outline-danger btn-eliminar-rol',
                                'data-nombre' => $role->name,
                                'title' => 'Eliminar'
                            ]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>


    </div>
</div>