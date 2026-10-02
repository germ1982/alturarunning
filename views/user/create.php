<?php
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UserSignupForm */
/** @var yii\web\View $model */

$this->title = 'Crear Nuevo Usuario';

?>

<div class="user-create container-fluid">
    <div class="neon-container mt-5">
        <div class="neon-title-container pb-3 d-flex align-items-center">
            <h2 class="neon-title-container"><?= Html::a('<i class="fas fa-reply"></i>', ['index'], ['class' => 'text-decoration-none text-light fs-4', 'title' => 'Volver']) ?>
            <span><?= Html::encode($this->title) ?></span></h2>
        </div>
        <hr style="border-color: rgba(255, 51, 51, 0.4);">

        <?php $form = ActiveForm::begin([
            'id' => 'form-user-create',
        ]); ?>

        <div class="row">
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'nombre')->textInput(['maxlength' => true, 'autocomplete' => 'given-name']) ?>
            </div>
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'apellido')->textInput(['maxlength' => true, 'autocomplete' => 'family-name']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'documento')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'autocomplete' => 'username']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'autocomplete' => 'email']) ?>
            </div>
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'telefono')->textInput(['maxlength' => true, 'autocomplete' => 'tel']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'password')->passwordInput(['autocomplete' => 'new-password']) ?>
            </div>
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'password_repeat')->passwordInput(['autocomplete' => 'new-password']) ?>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-outline-light']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>