<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var string $waNum */
/** @var string $callNum */

$this->title = 'Contactar a ' . $model->username;
?>
<div class="user-contacto row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="neon-container mt-5">
            <div class="neon-title-container pb-3"><?= Html::encode($this->title) ?></div>
            <?= $this->render('_form_contacto_opciones', [
                'waNum' => $waNum,
                'callNum' => $callNum,
            ]) ?>
            <div class="text-center mt-3">
                <?= Html::a('Volver', ['index'], ['class' => 'btn btn-outline-light btn-sm']) ?>
            </div>
        </div>
    </div>
</div>
