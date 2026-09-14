<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $model */

//$userid = Yii::$app->user->identity->id;
//$idrol_admin = User_rol::find()->where(['nombre' => 'Administrador'])->one()->idrol;
//$rol = User_usuario_rol::find()->where(['idusuario' => $userid,'idrol' => $idrol_admin])->one();
\yii\widgets\PjaxAsset::register($this);
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(['id' => 'form-usuario']); ?>

    <div id="mis-datos">

        <?= $form->field($model, 'id')->hiddenInput(['id' => 'input_iduser'])->label(false) ?>

        <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'telefono')->textInput(['maxlength' => true]) ?>

        <div class="d-flex justify-content-center">

            <?= Html::a('Cambiar Contraseña', '#', [
                'onclick' => "$('#mis-datos').hide(); $('#new-password').show(); $('.modal-footer').hide(); return false;"
            ]) ?>
        </div>
        <br>
        <!-- <div class="d-flex justify-content-center">
                  <?php //Html::submitButton('Guardar', ['class' => 'btn-donate']) 
                    ?>
            </div> -->




    </div>

    <div id='new-password' style="display:none">
        <div class="modal-title" style="margin-top: -15px; margin-bottom: -10px;">Cambiar Contraseñaaaaa</div>
        <hr>

        <div class="mb-3">
            <label class="control-label">Contraseña Actual</label>
            <?= Html::passwordInput('pass_actual', '', ['class' => 'form-control', 'id' => 'pass_actual', 'autocomplete' => 'new-password']) ?>
        </div>

        <div class="mb-3">
            <label class="control-label">Nueva Contraseña</label>
            <?= Html::passwordInput('pass_nueva', '', ['class' => 'form-control', 'id' => 'pass_nueva', 'autocomplete' => 'new-password']) ?>
        </div>

        <div class="mb-3">
            <label class="control-label">Repetir Nueva Contraseña</label>
            <?= Html::passwordInput('pass_repetir', '', ['class' => 'form-control', 'id' => 'pass_repetir', 'autocomplete' => 'new-password']) ?>
        </div>
        <br>
        <div class="div-boton d-flex justify-content-between align-items-center mt-4">
            <?= Html::button('Confirmar', ['class' => 'btn-donate', 'id' => 'btn-confirmar-pass']) ?>

            <?= Html::button('Cancelar', [
                'class' => 'btn-donate',
                'onclick' => "$('#new-password').hide(); $('#mis-datos').show(); $('.modal-footer').show();"
            ]) ?>
        </div>

        <div id="password-message" style="margin-top: 10px;"></div>

    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$urlCambio = Url::to(['user/change_password']);
$script = <<< JS

$('#btn-confirmar-pass').on('click', function() {

    var pActual  = $('#pass_actual').val();
    var pNueva   = $('#pass_nueva').val();
    var pRepetir = $('#pass_repetir').val();
    var iduser   = $('#input_iduser').val();

    if (pNueva === '' || pRepetir === '' || pActual === '') {
        $('#password-message').html('<span class="text-danger">Hay Campos Vacios.</span>');
        return;
    }

    if(pNueva !== pRepetir) {
        $('#password-message').html('<span class="text-danger">Las nuevas contraseñas no coinciden.</span>');
        return;
    }

    $.ajax({
        url: '$urlCambio',
        type: 'POST',
        data: {
            actual: pActual,
            nueva: pNueva,
            iduser: iduser,
            [yii.getCsrfParam()]: yii.getCsrfToken()
        },
        success: function(response) {
            console.log("Log del servidor:", response.log);
            
            // 1. PRIMERO evaluamos si el request fue exitoso en general
            if(response.success) {
                // 2. DESPUÉS evaluamos si es el propio usuario o un admin cambiando a otro
                if(response.is_self) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Contraseña actualizada!',
                        text: 'Tu sesión se cerrará por seguridad.',
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = response.redirect;
                    });
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        $('#new-password').hide();
                        $('#mis-datos').show();
                        $('.modal-footer').show();
                        $('#pass_actual, #pass_nueva, #pass_repetir').val('');
                        $('#password-message').html('');
                    });
                }
            } else {
                // Si response.success es falso (ej. contraseña actual incorrecta)
                $('#password-message').html('<span class="text-danger">' + response.message + '</span>');
            }
        },
        error: function(xhr) {
            console.log("Error HTTP: " + xhr.status);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo procesar la solicitud.',
            });
        }
    });
});

JS;
$this->registerJs($script);
?>