<?php

/** @var yii\web\View $this */
/** @var app\models\EventoForm $modelo */
/** @var array $personas */

$this->title = 'Editar evento';

echo $this->render('_form', ['modelo' => $modelo, 'personas' => $personas]);
