<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\User_rol;

/* @var $user app\models\user */
/* @var $rolesActuales array */
/** @var yii\web\View $user */
// Traigo TODOS los roles
$roles = User_rol::find()
    ->orderBy(['nombre' => SORT_ASC])
    ->all();
?>

<div id="form_principal">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">
            Roles de <?= Html::encode($user->username) ?>
        </h5>

        <button type="button"
            class="btn-plus"
            title="Crear rol"
            onclick="mostrar_alta_rol()">
            <i class="bi bi-plus-lg"></i> <span>Crear Rol</span>
        </button>
    </div>


    <?php $form = ActiveForm::begin([
        'id' => 'roles-form', // <--- AGREGAR ESTE ID
        'options' => ['data-pjax' => true] 
    ]); ?>

<div class="table-responsive">
    <table id="tabla_roles" class="table table-bordered table-hover table-sm">
        <thead class="thead-light">
            <tr>
                <th style="width:60px">ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th style="width:90px; text-align:center">Asignado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($roles as $rol): ?>
                <tr>
                    <td><?= $rol->idrol ?></td>
                    <td><?= Html::encode($rol->nombre) ?></td>
                    <td><?= Html::encode($rol->descripcion) ?></td>
                    <td style="text-align:center">
                        <input
                            type="checkbox"
                            name="roles[]"
                            value="<?= $rol->idrol ?>"
                            <?= in_array($rol->idrol, $rolesActuales) ? 'checked' : '' ?>>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
    <?php ActiveForm::end(); ?>
</div>

<div id="form_alta_rol" style="display:none;">
    <div class="row">
        <div class="col-md-9">
            <label for="rol-nombre">Nombre</label>
            <input type="text" id="rol-nombre" name="rol-nombre" maxlength="255" class="form-control">
        </div>
        <div class="col-md-3">
            <div class="form-check row " style="margin-top: 25px;">
                <div class="col-md-1">
                    <input type="checkbox" id="rol-activo" name="rol-activo" class="form-check-input" checked>
                </div>
                <div class="col-md-4">
                    <label for="rol-activo" class="form-check-label">Activo</label>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-md-12">
            <label for="rol-descripcion">Descripción</label>
            <input type="text" id="rol-descripcion" name="rol-descripcion" maxlength="255" class="form-control">
        </div>
    </div>
    <!-- Botones -->
    <div class="mt-3 text-end">
        <button type="button" class="btn btn-secondary" onclick="mostrar_form_principal()">Cerrar</button>
        <button type="button" class="btn btn-secondary" onclick="guardar_rol()">Guardar</button>
    </div>
</div>


<script>
    function mostrar_alta_rol() {
        $('#form_principal').hide();
        $('#form_alta_rol').show();
        $('#btnGuardar').hide();
        $('#btnCerrar').hide();
    }

    function mostrar_form_principal() {
        $('#form_alta_rol').hide();
        $('#form_principal').show();
        $('#btnGuardar').show();
        $('#btnCerrar').show();
    }


    function agregar_rol(idRol) {
    // Tomar los datos del form de alta
    let nombre = $('#rol-nombre').val();
    let descripcion = $('#rol-descripcion').val();
    let activo = $('#rol-activo').is(':checked') ? 1 : 0;

    // Crear el nuevo tr
    let fila = $(`
        <tr>
            <td>${idRol}</td>
            <td>${nombre}</td>
            <td>${descripcion}</td>
            <td class="text-center">
                <input type="checkbox" name="roles[]" value="${idRol}" ${activo ? 'checked' : ''}>
            </td>
        </tr>
    `);

    // Agregar la fila al tbody
    $('#tabla_roles tbody').append(fila);

    // Ordenar alfabéticamente por nombre
    ordenar_tabla_alfa();
// Limpiar campos
    $('#rol-nombre').val('');
    $('#rol-descripcion').val('');
    $('#rol-activo').prop('checked', true);

    // Volver a mostrar el form principal
    mostrar_form_principal();
}



    function guardar_rol() {

        let nombre = $('#rol-nombre').val();
        let descripcion = $('#rol-descripcion').val();

        return $.post('index.php?r=user_rol/guardar_rol', {
            nombre: nombre,
            descripcion: descripcion
        }).then(function(resp) {

            if (resp.ok) {
                agregar_rol(resp.idrol);
            } else {
                alert('Error al guardar rol');
                return 0;
            }

        });

    }

    function ordenar_tabla_alfa() {
    let rows = $('#tabla_roles tbody tr').get();

    rows.sort(function(a, b) {
        let A = $(a).children('td').eq(1).text().toUpperCase();
        let B = $(b).children('td').eq(1).text().toUpperCase();
        return (A < B) ? -1 : (A > B) ? 1 : 0;
    });

    $.each(rows, function(index, row) {
        $('#tabla_roles tbody').append(row);
    });
}
</script>