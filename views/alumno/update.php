<?php

/** @var yii\web\View $this */
/** @var app\models\AlumnoForm $modelo */
/** @var array $cuotas */
/** @var array $objetivos */
/** @var array $personasLibres */

$this->title = 'Editar alumno';

echo $this->render('_form', compact('modelo', 'cuotas', 'objetivos', 'personasLibres'));
