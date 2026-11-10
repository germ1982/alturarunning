<?php

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var array $rolesActuales */

$this->title = 'Roles de ' . $user->username;
?>
<div class="user-roles">
    <div class="neon-container mt-5">
        <?= $this->render('_form_roles', [
            'user' => $user,
            'rolesActuales' => $rolesActuales,
        ]) ?>
    </div>
</div>
