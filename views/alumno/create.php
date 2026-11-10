<?php

/** @var yii\web\View $this */
/** @var app\models\AlumnoForm $modelo */
/** @var array $cuotas */
/** @var array $objetivos */
/** @var array $personasLibres */

$this->title = 'Nuevo alumno';

echo $this->render('_form', compact('modelo', 'cuotas', 'objetivos', 'personasLibres'));
