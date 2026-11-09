<?php

use app\helpers\UserHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\User $model */

// Un administrador puede cambiar la contraseña de otro sin conocer la actual.
$pideActual = !UserHelper::esAdmin() || $model->id == Yii::$app->user->id;
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(['id' => 'form-usuario']); ?>

    <div id="mis-datos" class="neon-container mt-5">

        <div class="neon-title-container pb-3"><?= Html::encode($this->title) ?></div>

        <?= $form->field($model, 'id')->hiddenInput(['id' => 'input_iduser'])->label(false) ?>

        <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'telefono')->textInput(['maxlength' => true]) ?>

        <div class="d-flex justify-content-center mb-3">
            <?= Html::a('Cambiar Contraseña', '#', [
                'onclick' => "$('#mis-datos').hide(); $('#new-password').show(); return false;"
            ]) ?>
        </div>

        <div class="d-flex justify-content-center">
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-outline-light btn-sm']) ?>
        </div>
    </div>

    <div id="new-password" class="neon-container mt-5" style="display:none">
        <div class="neon-title-container pb-3">Cambiar Contraseña</div>

        <?php if ($pideActual): ?>
            <div class="mb-3">
                <label class="control-label" for="pass_actual">Contraseña Actual</label>
                <?= Html::passwordInput('pass_actual', '', ['class' => 'form-control', 'id' => 'pass_actual', 'autocomplete' => 'current-password']) ?>
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <label class="control-label" for="pass_nueva">Nueva Contraseña</label>
            <?= Html::passwordInput('pass_nueva', '', ['class' => 'form-control', 'id' => 'pass_nueva', 'autocomplete' => 'new-password']) ?>
        </div>

        <div class="mb-3">
            <label class="control-label" for="pass_repetir">Repetir Nueva Contraseña</label>
            <?= Html::passwordInput('pass_repetir', '', ['class' => 'form-control', 'id' => 'pass_repetir', 'autocomplete' => 'new-password']) ?>
        </div>

        <div class="d-flex justify-content-center gap-3 mt-4">
            <?= Html::button('Confirmar', ['class' => 'btn btn-outline-light btn-sm', 'id' => 'btn-confirmar-pass']) ?>

            <?= Html::button('Cancelar', [
                'class' => 'btn btn-outline-light btn-sm',
                'onclick' => "$('#new-password').hide(); $('#mis-datos').show();"
            ]) ?>
        </div>

        <div id="password-message" class="mt-3 text-center"></div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$urlCambio = Json::htmlEncode(Url::to(['user/change_password']));
$pideActualJs = Json::htmlEncode($pideActual);
$script = <<<JS

$('#btn-confirmar-pass').on('click', function() {
    var pideActual = $pideActualJs;
    var pActual  = pideActual ? $('#pass_actual').val() : '';
    var pNueva   = $('#pass_nueva').val();
    var pRepetir = $('#pass_repetir').val();
    var iduser   = $('#input_iduser').val();
    var aviso    = $('#password-message');

    if (pNueva === '' || pRepetir === '' || (pideActual && pActual === '')) {
        aviso.html('<span class="text-danger">Hay campos vacíos.</span>');
        return;
    }

    if (pNueva.length < 6) {
        aviso.html('<span class="text-danger">La nueva contraseña debe tener al menos 6 caracteres.</span>');
        return;
    }

    if (pNueva !== pRepetir) {
        aviso.html('<span class="text-danger">Las nuevas contraseñas no coinciden.</span>');
        return;
    }

    $.ajax({
        url: $urlCambio,
        type: 'POST',
        data: {
            actual: pActual,
            nueva: pNueva,
            iduser: iduser,
            [yii.getCsrfParam()]: yii.getCsrfToken()
        },
        success: function(response) {
            if (!response.success) {
                aviso.html($('<span class="text-danger">').text(response.message));
                return;
            }

            if (response.is_self) {
                mostrarAlerta('Tu sesión se cerrará por seguridad.', '¡Contraseña actualizada!', 'green', function() {
                    window.location.href = response.redirect;
                });
                return;
            }

            mostrarAlerta(response.message, '¡Éxito!', 'green', function() {
                $('#new-password').hide();
                $('#mis-datos').show();
                $('#pass_nueva, #pass_repetir').val('');
                aviso.html('');
            });
        },
        error: function() {
            mostrarAlerta('No se pudo procesar la solicitud.', 'Error');
        }
    });
});

JS;
$this->registerJs($script);
?>
