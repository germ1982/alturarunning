<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $model */
$this->title = 'Registrarse';

?>

<div class="row justify-content-center overlay-div centro-celu">
      <div class="col-md-3"></div>

      <div class="col-md-6 p-4">
            <div class="neon-container" style="padding-left: 1.5rem; text-align: left;">
                  <div class="neon-title-container pb-4 " style="text-align:center!important"><?= Html::encode($this->title) ?></div>
                  <br>

                  <?php $form = ActiveForm::begin(); ?>

                  <?= $form->field($model, 'nombre')->textInput(['maxlength' => true, 'autocomplete' => 'given-name']) ?>
                  <?= $form->field($model, 'apellido')->textInput(['maxlength' => true, 'autocomplete' => 'family-name']) ?>
                  <?= $form->field($model, 'documento')->textInput(['maxlength' => true]) ?>
                  <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
                  <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                  <?= $form->field($model, 'telefono')->textInput(['maxlength' => true]) ?>
                  <?= $form->field($model, 'password')->passwordInput() ?>
                  <?= $form->field($model, 'password_repeat')->passwordInput() ?>

                  <div class="d-flex justify-content-center">
                        <?= Html::submitButton('Crear cuenta', ['class' => 'btn btn-outline-light btn-sm']) ?>
                  </div>

                  <?php ActiveForm::end(); ?>
            </div>
      </div>

      <div class="col-md-3"></div>
</div>