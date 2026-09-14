<?php
      // BLOQUE PARA DISPARAR SWEETALERT
      // Este código solo se encarga de imprimir el JS de configuración, 
      // la librería ya la cargó el AppAsset.

      $flashes = Yii::$app->session->getAllFlashes();
      if ($flashes) {
            foreach ($flashes as $type => $message) {
                  $icon = 'info';
                  $title = 'Información';

                  if ($type === 'success') {
                        $icon = 'success';
                        $title = '¡Éxito!';
                  } elseif ($type === 'error' || $type === 'danger') {
                        $icon = 'error';
                        $title = 'Error';
                  } elseif ($type === 'warning') {
                        $icon = 'warning';
                        $title = 'Atención';
                  }

                  //$msgJson = \yii\helpers\Json::htmlEncode($message);
                  $msgJson = \yii\helpers\Json::encode($message);

                  $this->registerJs("
            Swal.fire({
                title: '$title',
                html: $msgJson,
                icon: '$icon',
                confirmButtonText: 'Aceptar'
            });
        ");
            }
      }
      ?>