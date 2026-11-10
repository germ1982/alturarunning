<?php

/** @var yii\web\View $this */
/** @var app\models\ProfesorForm $modelo */
/** @var array $especialidades */
/** @var array $personasLibres */

$this->title = 'Editar profesor';

echo $this->render('_form', compact('modelo', 'especialidades', 'personasLibres'));
