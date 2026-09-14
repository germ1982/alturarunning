<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Iniciar sesión';

/** @var yii/ $model */
?>
<div class="site-login pt-4 pb-5">



      <div class="row pt-5">

            <div class="col-lg-4">

            </div>
            <div class="col-lg-4 ">
                  <div class="neon-container" style="padding-left: 1.5rem; text-align: center;">
                        <div class="neon-title-container pb-4"><?= Html::encode($this->title) ?></div>

                        <?php $form = ActiveForm::begin(['id' => 'login-form',]); ?>

                        <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'class' => 'form-control', 'autocomplete' => 'username'])->label('Usuario') ?>
                        <?= $form->field($model, 'password')->passwordInput(['class' => 'form-control', 'autocomplete' => 'new-password'])->label('Contraseña') ?>
                        <?= $form->field($model, 'rememberMe')->checkbox()->label('Mantener sesión iniciada') ?>


                        <div class="form-group">
                              <div class="d-flex justify-content-center">
                                    <?= Html::submitButton('Iniciar', ['class' => 'btn btn-outline-light btn-sm', 'name' => 'login-button']) ?>
                              </div>
                        </div>

                        <div style="margin-top:15px;" class="d-flex justify-content-center">
                              <?= Html::a('¿No tenés cuenta? Registrate acá', ['/site/registro'], [
                                    'style' => 'font-size:14px; text-decoration: underline;'
                              ]) ?>
                        </div>

                        <?php if ($model->hasErrors('password')): ?>
                              <div style="margin-top:15px;">
                                    <?= Html::a('¿Olvidaste tu contraseña?', ['/site/solicitar_reset'], [
                                          'style' => 'font-size:14px; text-decoration: underline; display:block; margin-top:8px;'
                                    ]) ?>
                              </div>
                        <?php endif; ?>

                        <?php ActiveForm::end(); ?>



                  </div>
            </div>
            <div class="col-lg-4">

            </div>
      </div>
</div>