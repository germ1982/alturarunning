<?php

use yii\helpers\Html;

?>

<footer id="footer" class="mt-auto py-3 bg-light">
      <div class="container">
            <div class="row text-muted">
                  <div class="col-md-6 text-center text-md-start footer-link">
                        <?= 
                        //\yii\helpers\Html::a('&copy; G.E.R.M. Servicios En Informatica ' . date('Y'), 'https://www.tiktok.com/@g.e.r.m5/video/7625761791905172756?is_from_webapp=1&sender_device=pc&web_id=7649578402966144532', 
                              \yii\helpers\Html::a('&copy; G.E.R.M. Servicios En Informatica ' . date('Y'), 'https://germ1982.github.io/germweb/', 
                              [
                              'target' => '_blank',
                              'style' => 'text-decoration: none;'
                        ]) 
                        ?>
                  </div>
                  <div class="col-md-6 text-center text-md-end">

                        <?= Html::a('Acerca de Comunidad Del Valle', ['/site/about'], ['class' => 'footer-link']) ?>
                        &nbsp; | &nbsp;
                        <?= Html::a('Contacto', ['/site/contact'], ['class' => 'footer-link']) ?>

                  </div>
            </div>
      </div>
</footer>