<?php

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = 'Editar usuario: ' . $model->username;
?>
<div class="user-update row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
