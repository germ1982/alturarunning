<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Recuperar contraseña';
?>
<div class="site-solicitar-reset row justify-content-center pt-5">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="neon-container text-center" style="padding-left: 1.5rem;">
            <div class="neon-title-container pb-3"><?= Html::encode($this->title) ?></div>
            <p>Pedile a tu profesor o a un administrador de la escuela que te genere una contraseña temporal.</p>
            <?= Html::a('Volver a iniciar sesión', ['site/login'], ['class' => 'btn btn-outline-light btn-sm']) ?>
        </div>
    </div>
</div>
