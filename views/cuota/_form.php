<?php

/** @var yii\web\View $this */
/** @var app\models\Cuota $cuota */

use app\models\ConstantesGlobales;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
?>
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="neon-container mt-5">
            <div class="neon-title-container pb-3"><?= Html::encode($this->title) ?></div>

            <?php $form = ActiveForm::begin(['id' => 'form-cuota']); ?>
            <?= $form->field($cuota, 'mes')->input('month', ['style' => 'color-scheme: dark;', 'placeholder' => 'AAAA-MM']) ?>
            <?= $form->field($cuota, 'tipo')->dropDownList(ConstantesGlobales::TIPOS_CUOTA) ?>
            <?= $form->field($cuota, 'monto')->input('number', ['min' => 0, 'step' => '0.01']) ?>

            <div class="d-flex justify-content-center gap-3 mt-4">
                <?= Html::submitButton('Guardar', ['class' => 'btn btn-outline-light btn-sm']) ?>
                <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-outline-light btn-sm']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
