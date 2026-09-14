<?php
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UserSignupForm */ // <--- Ojo acá, usá el FormModel

?>

<div class="user-create">
    <?php $form = ActiveForm::begin([
        'id' => 'form-usuario', // ID para que el JS lo encuentre
        'enableAjaxValidation' => false,
    ]); ?>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'autofocus' => true]) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'telefono')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'password')->passwordInput() ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'password_repeat')->passwordInput() ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>