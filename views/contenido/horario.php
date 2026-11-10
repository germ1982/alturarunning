<?php

/** @var yii\web\View $this */
/** @var app\models\Calendario $horario */
/** @var array $lugares */
/** @var array $modalidades */
/** @var array $profesores */

use app\models\ConstantesGlobales;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = $horario->isNewRecord ? 'Nuevo horario' : 'Editar horario';
?>
<div class="contenido-horario row justify-content-center mt-5 mb-5">
    <div class="col-12 col-md-8 col-lg-6">
        <?= $this->render('_encabezado', ['volver' => ['horarios']]) ?>

        <div class="neon-container">
            <?php $form = ActiveForm::begin(['id' => 'form-horario']); ?>
            <?= $form->field($horario, 'idlugar')->dropDownList($lugares, ['prompt' => 'Elegí un lugar...']) ?>
            <div class="row">
                <div class="col-6"><?= $form->field($horario, 'dia')->dropDownList(ConstantesGlobales::DIAS, ['prompt' => 'Elegí...']) ?></div>
                <div class="col-6"><?= $form->field($horario, 'horario')->textInput(['maxlength' => 20, 'placeholder' => '20:00 hs.']) ?></div>
            </div>
            <?= $form->field($horario, 'idmodalidad')->dropDownList($modalidades, ['prompt' => 'Elegí una modalidad...']) ?>
            <?= $form->field($horario, 'idinstructor')->dropDownList($profesores, ['prompt' => 'Elegí un profesor...']) ?>

            <div class="d-flex justify-content-center gap-3 mt-4">
                <?= Html::submitButton('Guardar', ['class' => 'btn btn-outline-light']) ?>
                <?= Html::a('Cancelar', ['horarios'], ['class' => 'btn btn-outline-light']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
