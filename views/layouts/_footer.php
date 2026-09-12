<?php

use yii\helpers\Html;

?>

<style><?php include __DIR__ . '/layout.css'; ?></style>

<footer id="footer">
    <div class="container">
        <div class="footer-container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                    <a href="https://germ1982.github.io/germweb/" target="_blank" style=" font-weight: 500;">
                        &copy; G.E.R.M. Servicios En Informatica 2026
                    </a>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <?= Html::a('Acerca de Altura Running Team', ['site/about']) ?>
                    &nbsp; | &nbsp;
                    <?= Html::a('Contacto', ['site/contact']) ?>
                </div>
            </div>
        </div>
    </div>
</footer>